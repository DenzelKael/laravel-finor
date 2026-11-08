<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Subscription;

class DashboardStats
{
    public function getActiveClients(): int
    {
        return Client::whereHas(
            'subscriptions',
            fn ($query) => $query->active()
        )->count();
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
            ->whereBetween('expiration_date', [
                today(),
                today()->addDays($days),
            ])
            ->count();
    }

    public function getChartData(): array
    {
        $startOfYear = now()->copy()->startOfYear();
        $endOfYear = now()->copy()->endOfYear();

        $payments = Payment::query()
            ->select([
                'payment_date',
                'amount',
            ])
            ->where('status', PaymentStatus::Registered->value)
            ->whereBetween('payment_date', [
                $startOfYear->toDateString(),
                $endOfYear->toDateString(),
            ])
            ->orderBy('payment_date')
            ->get();

        $grouped = $payments
            ->groupBy(
                fn (Payment $payment) => $payment->payment_date->weekOfYear
            )
            ->sortKeys();

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
