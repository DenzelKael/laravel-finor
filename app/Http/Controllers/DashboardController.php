<?php

namespace App\Http\Controllers;

use App\Services\DashboardStats;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardStats $stats) {}

    public function index(): View
    {
        $activeClients = $this->stats->getActiveClients();
        $monthlyRevenue = $this->stats->getMonthlyRevenue();
        $expiringSubscriptions = $this->stats->getExpiringSubscriptions();

        return view('dashboard.index', compact(
            'activeClients',
            'monthlyRevenue',
            'expiringSubscriptions'
        ));
    }

    public function chartData(): JsonResponse
    {
        return response()->json($this->stats->getChartData());
    }
}
