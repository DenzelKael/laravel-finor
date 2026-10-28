<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriptionReportService
{
    /**
     * Obtiene las suscripciones activas que vencen en el rango de días especificado.
     */
    public function getExpiringSubscriptions(int $days = 15, int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $allowedDays = [7, 15, 30];
        if (! in_array($days, $allowedDays, true)) {
            $days = 15;
        }

        $query = Subscription::query()
            ->where('status', SubscriptionStatus::Active->value)
            ->whereBetween('expiration_date', [today(), today()->addDays($days)])
            ->with(['client', 'plan'])
            ->orderBy('expiration_date', 'asc');

        if (! empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('plan', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene los conteos de suscripciones por vencer para los rangos de 7, 15 y 30 días.
     *
     * @return array<int, int>
     */
    public function getExpiringCounts(): array
    {
        $today = today();

        return [
            7 => Subscription::query()
                ->where('status', SubscriptionStatus::Active->value)
                ->whereBetween('expiration_date', [$today, $today->copy()->addDays(7)])
                ->count(),
            15 => Subscription::query()
                ->where('status', SubscriptionStatus::Active->value)
                ->whereBetween('expiration_date', [$today, $today->copy()->addDays(15)])
                ->count(),
            30 => Subscription::query()
                ->where('status', SubscriptionStatus::Active->value)
                ->whereBetween('expiration_date', [$today, $today->copy()->addDays(30)])
                ->count(),
        ];
    }
}
