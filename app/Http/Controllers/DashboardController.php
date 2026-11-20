<?php

namespace App\Http\Controllers;

use App\Services\DashboardStats;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardStats $stats) {}

    public function index(): View
    {
        $activeClients = $this->stats->getActiveClients();
        $monthlyRevenue = $this->stats->getMonthlyRevenue();
        $expiringSubscriptions = $this->stats->getExpiringSubscriptions();

        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();

        $unreadNotifications = Auth::user()
            ->unreadNotifications()
            ->count();

        return view('dashboard.index', compact(
            'activeClients',
            'monthlyRevenue',
            'expiringSubscriptions',
            'notifications',
            'unreadNotifications'
        ));
    }

    public function chartData(): JsonResponse
    {
        return response()->json($this->stats->getChartData());
    }
}
