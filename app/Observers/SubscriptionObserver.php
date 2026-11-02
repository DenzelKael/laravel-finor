<?php

namespace App\Observers;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Support\AuditLogger;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        AuditLogger::log(
            'subscription.created',
            $subscription,
            [
                'client_id' => $subscription->client_id,
                'plan_id' => $subscription->plan_id,
                'expiration_date' => $subscription->expiration_date?->toDateString(),
            ],
            "Suscripción #{$subscription->id} creada"
        );
    }

    public function updated(Subscription $subscription): void
    {
        if (
            $subscription->wasChanged('status')
            && $subscription->status === SubscriptionStatus::Cancelled
        ) { // ajusta el nombre del caso
            AuditLogger::log(
                'subscription.cancelled',
                $subscription,
                ['old' => $subscription->getOriginal('status')],
                "Suscripción #{$subscription->id} cancelada"
            );

            return;
        }

        if ($subscription->wasChanged('expiration_date')) {
            AuditLogger::log(
                'subscription.renewed',
                $subscription,
                [
                    'old' => $subscription->getOriginal('expiration_date'),
                    'new' => $subscription->expiration_date?->toDateString(),
                ],
                "Suscripción #{$subscription->id} renovada"
            );
        }
    }
}
