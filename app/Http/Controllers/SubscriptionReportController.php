<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionReportController extends Controller
{
    public function __construct(
        private SubscriptionReportService $reportService
    ) {}

    /**
     * Reporte de suscripciones por vencer (7, 15 y 30 días).
     */
    public function expiringSubscriptions(Request $request): View|JsonResponse
    {
        $days = (int) $request->input('days', 15);
        $search = $request->input('search');

        $subscriptions = $this->reportService->getExpiringSubscriptions($days, 15, $search);
        $counts = $this->reportService->getExpiringCounts();

        if ($request->expectsJson()) {
            return response()->json([
                'subscriptions' => $subscriptions,
                'counts' => $counts,
                'days' => $days,
            ]);
        }

        return view('reports.expiring_subscriptions', compact(
            'subscriptions',
            'counts',
            'days',
            'search'
        ));
    }
}
