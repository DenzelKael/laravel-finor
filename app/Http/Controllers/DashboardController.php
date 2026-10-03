<?php

namespace App\Http\Controllers;

use App\Services\DashboardStats;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardStats $stats;

    public function __construct(DashboardStats $stats)
    {
        $this->stats = $stats;
    }

public function index()
    {
        $activeClients = $this->stats->getActiveClients();
        $monthlyRevenue = $this->stats->getMonthlyRevenue();
        $expiringSubscriptions = $this->stats->getExpiringSubscriptions();

        return view('dashboard.index', compact('activeClients', 'monthlyRevenue', 'expiringSubscriptions'));
    }
    public function chartData()
    {
        $data = $this->stats->getChartData();
        return response()->json($data);
    }
}