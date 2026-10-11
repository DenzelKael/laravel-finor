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
        $payments = Payment::whereYear('payment_date', now()->year)->get();

        $grouped = $payments->groupBy(function ($payment) {
            return \Carbon\Carbon::parse($payment->payment_date)->weekOfYear;
        })->sortKeys();

        $labels = [];
        $data = [];

        foreach ($grouped as $week => $weekPayments) {
            $labels[] = 'Semana ' . $week;

            $data[] = (float) $weekPayments->sum('amount');
        }
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}