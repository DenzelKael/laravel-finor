<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Vista del listado de suscripciones
     */
    public function index()
    {
        return view('subscriptions.index');
    }

    /**
     * Listado de suscripciones en formato JSON (con relaciones cargadas)
     */
    public function data(): JsonResponse
    {
        $subscriptions = Subscription::with(['client', 'plan'])->get();

        return response()->json(
            SubscriptionResource::collection($subscriptions)
        );
    }

    /**
     * Cargar dependencias y formulario para el alta
     */
    public function create(Request $request)
    {
        $clients = Client::all();
        $plans = Plan::activo()->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'clients' => ClientResource::collection($clients),
                'plans' => PlanResource::collection($plans),
            ]);
        }

        return view('subscriptions.create', compact('clients', 'plans'));
    }

    /**
     * Alta / Creación
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->subscriptionService->createSubscription($request->validated());

        return response()->json([
            'message' => 'Suscripción creada exitosamente',
            'data' => new SubscriptionResource($subscription),
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
    public function renew(Subscription $subscription): JsonResponse
    {
        $subscription = $this->subscriptionService->renew($subscription);

        return response()->json([
            'message' => 'Suscripción renovada exitosamente',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    /**
     * Acción del dominio: Cancelar
     */
    public function cancel(Subscription $subscription): JsonResponse
    {
        $subscription = $this->subscriptionService->cancel($subscription);

        return response()->json([
            'message' => 'Suscripción cancelada exitosamente',
            'data' => new SubscriptionResource($subscription),
        ]);
    }
}
