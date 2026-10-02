<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuscripcionRequest;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Suscripcion;
use Carbon\Carbon;

class SuscripcionController extends Controller
{
    public function index()
    {
    $suscripciones = Suscripcion::with(['client', 'plan'])
        ->latest()
        ->paginate(10);

    return view('suscriptions.index', compact('suscripciones'));
    }

    public function create()
{
    $clients = Client::orderBy('name')->get();

    $plans = Plan::where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view('suscriptions.form', compact('clients', 'plans'));
}

    public function store(SuscripcionRequest $request)
    {
    $data = $request->validated();

    $plan = Plan::findOrFail($data['plan_id']);

    $fechaInicio = Carbon::parse($data['fecha_inicio']);

    $fechaFin = $fechaInicio->copy()
        ->addDays($plan->duracion_dias);

    $data['fecha_fin'] = $fechaFin;

    Suscripcion::create($data);

    return redirect()
        ->route('suscriptions.index')
        ->with('success', 'Suscripción creada correctamente.');
    }


}