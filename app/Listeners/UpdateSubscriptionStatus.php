<?php

namespace App\Listeners;

use App\Events\PaymentRegistered;

class UpdateSubscriptionStatus
{
    public function handle(PaymentRegistered $event): void
    {
        $event->payment->loadMissing(
            'subscription.plan'
        );

        $subscription = $event->payment->subscription;

        if (! $subscription) {
            return;
        }

        $subscription->syncStatusFromPayments();
    }
}