<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Subscription;
use Carbon\Carbon;

class DashboardStats
{
    public function getActiveClients(): int
    {
        return Client::whereHas('subscriptions', fn ($query) => $query->active())->count();
    }

    public function getMonthlyRevenue(): float
    {
        return (float) Payment::query()
            ->totalOfMonth()
            ->sum('amount');
    }

    public function getExpiringSubscriptions(int $days = 15): int
    {
        return Subscription::query()
            ->where('status', SubscriptionStatus::Active->value)
            ->whereBetween('expiration_date', [today(), today()->addDays($days)])
            ->count();
    }

    public function getChartData(): array
    {
        $payments = Payment::query()
            ->where('status', PaymentStatus::Registered->value)
            ->whereYear('payment_date', now()->year)
            ->orderBy('payment_date')
            ->get();

        $grouped = $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->payment_date)->weekOfYear;
        })->sortKeys();

        $labels = [];
        $data = [];

        foreach ($grouped as $week => $weekPayments) {
            $labels[] = 'Semana '.$week;
            $data[] = (float) $weekPayments->sum('amount');
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
