<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Http\Resources\ClientResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Listado de suscripciones (con relaciones cargadas)
     */
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::with(['client', 'plan'])->get();
        
        return response()->json(
            SubscriptionResource::collection($subscriptions)
        );
    }

    /**
     * Cargar dependencias para el formulario de alta
     */
    public function create(): JsonResponse
    {
        // En Client no hay concepto de 'activo', así que los enviamos todos.
        // En Plan usamos el scope 'activo()' de tu modelo.
        $clients = Client::all();
        $plans = Plan::activo()->get();

        return response()->json([
            'clients' => ClientResource::collection($clients),
            'plans' => PlanResource::collection($plans)
        ]);
    }

    /**
     * Alta / Creación
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'start_date' => ['nullable', 'date'],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        
        // Si no mandan fecha de inicio, por defecto es hoy
        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : today();
        
        // Calculamos end_date sumando los días del plan
        $endDate = $startDate->copy()->addDays($plan->duracion_dias);

        $subscription = Subscription::create([
            'client_id' => $validated['client_id'],
            'plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => SubscriptionStatus::Active,
        ]);

        $subscription->load(['client', 'plan']);

        return response()->json([
            'message' => 'Suscripción creada exitosamente',
            'data' => new SubscriptionResource($subscription)
        ], 201);
    }

    /**
     * Consulta específica
     */
    public function show(Subscription $subscription): JsonResponse
    {
        $subscription->load(['client', 'plan']);
        
        return response()->json(
            new SubscriptionResource($subscription)
        );
    }

    /**
     * Acción del dominio: Renovar
     */
    public function renew(Request $request, Subscription $subscription): JsonResponse
    {
        $plan = $subscription->plan;
        
        // Si la suscripción ya venció, la renovación aplica desde hoy.
        // Si aún está activa, suma los días a la fecha de vencimiento actual.
        $baseDate = $subscription->isExpired() ? today() : Carbon::parse($subscription->end_date);
        
        $newEndDate = $baseDate->addDays($plan->duracion_dias);

        $subscription->update([
            'end_date' => $newEndDate,
            'status' => SubscriptionStatus::Active,
        ]);

        $subscription->load(['client', 'plan']);

        return response()->json([
            'message' => 'Suscripción renovada exitosamente',
            'data' => new SubscriptionResource($subscription)
        ]);
    }

    /**
     * Acción del dominio: Cancelar
     */
    public function cancel(Subscription $subscription): JsonResponse
    {
        $subscription->update([
            'status' => SubscriptionStatus::Cancelled,
        ]);

        $subscription->load(['client', 'plan']);

        return response()->json([
            'message' => 'Suscripción cancelada exitosamente',
            'data' => new SubscriptionResource($subscription)
        ]);
    }
}
