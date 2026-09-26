<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Requerimiento: Query Builder
            // Intenta consultar la base de datos real
            $clientesActivos = DB::table('clients')->where('status', 'activo')->count();
            
            $ingresosMes = DB::table('payments')
                ->whereMonth('created_at', now()->month)
                ->sum('amount');
                
            $suscripcionesPorVencer = DB::table('subscriptions')
                ->whereBetween('end_date', [now(), now()->addDays(7)])
                ->count();
                
        } catch (\Exception $e) {
            // Si las tablas de los otros equipos aún no existen, usamos datos de prueba para no bloquear el diseño
            $clientesActivos = 124;
            $ingresosMes = 3580.50;
            $suscripcionesPorVencer = 8;
        }

        return view('dashboard.index', compact('clientesActivos', 'ingresosMes', 'suscripcionesPorVencer'));
    }

    public function chartData()
    {
        // Endpoint para DOM/Fetch y Chart.js
        $datos = [
            'labels' => ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            'data' => [150, 300, 250, 400] 
        ];
        
        return response()->json($datos);
    }
}