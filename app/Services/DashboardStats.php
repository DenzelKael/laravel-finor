<?php

namespace App\Services;

use App\Models\Client;
// Quitamos la importación de Payment temporalmente porque no existe
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DashboardStats
{
    public function getActiveClients(): int
    {
        try {
            return Client::count(); 
        } catch (QueryException $e) {
            Log::error('Error cargando clientes activos: ' . $e->getMessage());
            return 0;
        }
    }
    public function getMonthlyRevenue(): float
    {
        return (float) rand(1000, 5000);
    }
    public function getChartData(): array
    {
        return [
            'labels' => ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            'data' => [rand(200, 800), rand(200, 800), rand(200, 800), rand(200, 800)]
        ];
    }
    public function getExpiringSubscriptions(): int
    {
        return rand(2, 15);
    }
}