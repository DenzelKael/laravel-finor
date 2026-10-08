<?php

namespace App\Http\Controllers;

use App\Services\DashboardStats;

class DashboardController extends Controller
{
    public function __construct(private DashboardStats $stats) {}

    public function index()
    {
        $activeClients = $this->stats->getActiveClients();
        $monthlyRevenue = $this->stats->getMonthlyRevenue();
        $expiringSubscriptions = $this->stats->getExpiringSubscriptions();

        return view('dashboard.index', compact('activeClients', 'monthlyRevenue', 'expiringSubscriptions'));
    }

    public function chartData()
    {
        return response()->json($this->stats->getChartData());
    }
}