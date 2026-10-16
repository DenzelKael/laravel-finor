<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $plans = Plan::latest()->paginate(10);

        return view('plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $plan = new Plan();

        return view('plans.create', compact('plan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request): JsonResponse
    {
        $plan = Plan::create($request->validated());

        return response()->json([
            'message' => 'Plan creado correctamente.',
            'data' => new PlanResource($plan),
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan): View
    {
        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanRequest $request, Plan $plan): JsonResponse
    {
        $plan->update($request->validated());

        return response()->json([
            'message' => 'Plan actualizado correctamente.',
            'data' => new PlanResource($plan),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan): JsonResponse
    {
        if ($plan->subscriptions()->exists()) {
            return response()->json([
                'message' => 'El plan tiene suscripciones; desactívelo en lugar de eliminarlo.',
            ], 422);
        }

        $plan->delete();

        return response()->json([
            'message' => 'Plan eliminado correctamente.',
        ]);
    }
}
