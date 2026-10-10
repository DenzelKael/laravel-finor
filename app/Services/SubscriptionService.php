<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubscriptionService
{
    public function create(Client $client, Plan $plan): Subscription
    {
        if (! $plan->activo) {
            throw ValidationException::withMessages([
                'plan_id' => 'No se puede contratar un plan inactivo.',
            ]);
        }

        return DB::transaction(function () use ($client, $plan) {
            $startDate = today();

            $subscription = new Subscription([
                'start_date' => $startDate->toDateString(),
                'expiration_date' => $startDate
                    ->copy()
                    ->addDays($plan->duracion_dias)
                    ->toDateString(),
                'status' => SubscriptionStatus::Active,
            ]);

            $subscription->client()->associate($client);
            $subscription->plan()->associate($plan);
            $subscription->save();

            return $subscription->load(['client', 'plan']);
        });
    }

    public function renew(Subscription $subscription): Subscription
    {
        if ($subscription->status === SubscriptionStatus::Cancelled) {
            throw ValidationException::withMessages([
                'subscription' => 'No se puede renovar una suscripción cancelada.',
            ]);
        }

        return DB::transaction(function () use ($subscription) {
            $subscription = Subscription::query()
                ->lockForUpdate()
                ->findOrFail($subscription->id);

            if ($subscription->status === SubscriptionStatus::Cancelled) {
                throw ValidationException::withMessages([
                    'subscription' => 'No se puede renovar una suscripción cancelada.',
                ]);
            }

            $plan = $subscription->plan;

            if (! $plan->activo) {
                throw ValidationException::withMessages([
                    'subscription' => 'No se puede renovar con un plan inactivo.',
                ]);
            }

            $today = today();
            $currentExpiration = $subscription->expiration_date;

            $newStartDate = $currentExpiration->greaterThanOrEqualTo($today)
                ? $currentExpiration->copy()->addDay()
                : $today->copy();

            $subscription->start_date = $newStartDate->toDateString();
            $subscription->expiration_date = $newStartDate
                ->copy()
                ->addDays($plan->duracion_dias)
                ->toDateString();
            $subscription->status = SubscriptionStatus::Active;
            $subscription->save();

            return $subscription->load(['client', 'plan']);
        });
    }

    public function cancel(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            $subscription = Subscription::query()
                ->lockForUpdate()
                ->findOrFail($subscription->id);

            if ($subscription->status === SubscriptionStatus::Cancelled) {
                throw ValidationException::withMessages([
                    'subscription' => 'La suscripción ya está cancelada.',
                ]);
            }

            $subscription->status = SubscriptionStatus::Cancelled;
            $subscription->save();

            return $subscription->load(['client', 'plan']);
        });
    }
}
