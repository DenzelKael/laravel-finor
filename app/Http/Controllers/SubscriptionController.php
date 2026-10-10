<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Subscription::class);

        $subscriptions = Subscription::with(['client', 'plan'])
            ->latest()
            ->paginate(10);

        return view('subscriptions.index', compact('subscriptions'));
    }

    public function create(): View
    {
        $this->authorize('create', Subscription::class);

        $clients = Client::orderBy('name')->get();
        $plans = Plan::activo()->orderBy('nombre')->get();

        return view('subscriptions.create', compact('clients', 'plans'));
    }

    public function store(
        StoreSubscriptionRequest $request,
        SubscriptionService $service
    ): JsonResponse {
        $client = Client::findOrFail($request->validated('client_id'));
        $plan = Plan::findOrFail($request->validated('plan_id'));

        $subscription = $service->create($client, $plan);

        return response()->json([
            'message' => 'Suscripción registrada correctamente.',
            'data' => new SubscriptionResource($subscription),
        ], 201);
    }

    public function renew(
        Subscription $subscription,
        SubscriptionService $service
    ): JsonResponse {
        $this->authorize('renew', $subscription);

        $subscription = $service->renew($subscription);

        return response()->json([
            'message' => 'Suscripción renovada correctamente.',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    public function cancel(
        Subscription $subscription,
        SubscriptionService $service
    ): JsonResponse {
        $this->authorize('cancel', $subscription);

        $subscription = $service->cancel($subscription);

        return response()->json([
            'message' => 'Suscripción cancelada correctamente.',
            'data' => new SubscriptionResource($subscription),
        ]);
    }
}