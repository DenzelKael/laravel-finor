<?php

namespace App\Listeners;

use App\Enums\SubscriptionStatus;
use App\Events\PaymentRegistered;

class UpdateSubscriptionStatus
{
    public function handle(PaymentRegistered $event): void
    {
        $event->payment->loadMissing('subscription.plan');

        $subscription = $event->payment->subscription;

        if (! $subscription) {
            return;
        }

        if (
            $subscription->isFullyPaid()
            && $subscription->status !== SubscriptionStatus::Paid
        ) {
            $subscription->update([
                'status' => SubscriptionStatus::Paid,
            ]);
        }
    }
}