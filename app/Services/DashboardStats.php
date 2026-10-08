<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Subscription;

class DashboardStats
{
    public function getActiveClients(): int
    {
        return Client::whereHas('subscriptions', function ($query) {
            $query->whereDate('expiration_date', '>=', now());
        })->count();
    }
    public function getMonthlyRevenue(): float
    {
        return (float) Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');
    }
    public function getExpiringSubscriptions(): int
    {
        return Subscription::whereBetween('expiration_date', [now(), now()->addDays(15)])->count();
    }
    public function getChartData(): array
    {
        $payments = Payment::selectRaw('WEEK(payment_date) as week, SUM(amount) as total')
            ->whereYear('payment_date', now()->year)
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $labels = [];
        $data = [];

        foreach ($payments as $payment) {
            $labels[] = 'Semana ' . $payment->week;
            $data[] = (float) $payment->total;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}