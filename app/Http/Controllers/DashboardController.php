<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Subscription;

class DashboardController extends Controller
{
   public function index()
    {

        $clientesActivos = Client::whereHas(
            'subscriptions',
            fn ($q) => $q->active()
        )->count();

        $ingresosMes = (float) Payment::totalOfMonth()->sum('amount');

        $suscripcionesPorVencer = Subscription::active()
            ->whereBetween('expiration_date', [
                today()->toDateString(),
                today()->addDays(7)->toDateString(),
            ])
            ->count();

        return view('dashboard.index', compact('clientesActivos', 'ingresosMes', 'suscripcionesPorVencer'));
    }

    public function chartData()
    {
        // Endpoint para DOM/Fetch y Chart.js
        $datos = [
            'labels' => ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            'data' => [150, 300, 250, 400],
        ];

        return response()->json($datos);
    }
}
