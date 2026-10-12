<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Crear una nueva suscripción calculando la fecha de expiración basada en el plan.
     */
    public function createSubscription(array $data): Subscription
    {
        $hasActive = Subscription::where('client_id', $data['client_id'])
            ->active()
            ->exists();

        if ($hasActive) {
            abort(422, 'El cliente ya cuenta con una suscripción activa.');
        }

        $plan = Plan::findOrFail($data['plan_id']);

        $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date'])->toDateString() : today()->toDateString();
        $expirationDate = Carbon::parse($startDate)->addDays($plan->duracion_dias)->toDateString();

        $subscription = Subscription::create([
            'client_id' => $data['client_id'],
            'plan_id' => $plan->id,
            'start_date' => $startDate,
            'expiration_date' => $expirationDate,
            'status' => SubscriptionStatus::Active,
        ]);

        return $subscription->load(['client', 'plan']);
    }

    /**
     * Método subscribe() según el requerimiento de la tarjeta escalable.
     */
    public function subscribe(\App\Models\Client $client, Plan $plan, ?string $startDate = null): Subscription
    {
        return $this->createSubscription([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'start_date' => $startDate,
        ]);
    }

    /**
     * Renovar una suscripción calculando la nueva fecha de expiración.
     * Aborta con HTTP 422 si la suscripción está cancelada.
     */
    public function renew(Subscription $subscription): Subscription
    {
        if ($subscription->status === SubscriptionStatus::Cancelled || $subscription->status === SubscriptionStatus::Cancelled->value) {
            abort(422, 'No se puede renovar una suscripción cancelada.');
        }

        $plan = $subscription->plan;

        $baseDate = $subscription->isExpired() ? today() : Carbon::parse($subscription->expiration_date);
        $newExpirationDate = $baseDate->copy()->addDays($plan->duracion_dias)->toDateString();

        $subscription->update([
            'expiration_date' => $newExpirationDate,
            'status' => SubscriptionStatus::Active,
        ]);

        return $subscription->load(['client', 'plan']);
    }

    /**
     * Cancelar una suscripción.
     * Aborta con HTTP 422 si ya se encuentra cancelada.
     */
    public function cancel(Subscription $subscription): Subscription
    {
        if ($subscription->status === SubscriptionStatus::Cancelled || $subscription->status === SubscriptionStatus::Cancelled->value) {
            abort(422, 'La suscripción ya se encuentra cancelada.');
        }

        $subscription->update([
            'status' => SubscriptionStatus::Cancelled,
        ]);

        return $subscription->load(['client', 'plan']);
    }
}
