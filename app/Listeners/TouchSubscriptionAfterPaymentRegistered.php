<?php

namespace App\Listeners;

use App\Events\PaymentRegistered;

class TouchSubscriptionAfterPaymentRegistered
{
    public function handle(PaymentRegistered $event): void
    {
        $subscription = $event->payment->subscription;

        if (! $subscription) {
            return;
        }

        /*
         * Un pago modifica información relacionada con la suscripción,
         * por lo que se actualiza su timestamp.
         *
         * El ciclo de vida permanece independiente:
         * ACTIVE / EXPIRED / CANCELLED.
         *
         * El estado financiero se calcula dinámicamente mediante
         * paidAmount(), pendingBalance() e isFullyPaid().
         */
        $subscription->touch();
    }
}