<?php

namespace App\Listeners;

use App\Events\PaymentVoided;

class UpdateSubscriptionStatusAfterPaymentVoided
{
    public function handle(PaymentVoided $event): void
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