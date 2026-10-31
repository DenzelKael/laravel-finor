<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Support\AuditLogger;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        AuditLogger::log(
            'payment.created',
            $payment,
            [
                'subscription_id' => $payment->subscription_id,
                'amount' => (string) $payment->amount,
                'payment_method' => $payment->payment_method?->value,
            ],
            "Pago #{$payment->id} registrado por {$payment->formatted_amount}"
        );
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        if (! $payment->wasChanged('status')) {
            return;
        }

        if ($payment->status === PaymentStatus::Voided) {
            AuditLogger::log(
                'payment.voided',
                $payment,
                ['reason' => $payment->void_reason],
                "Pago #{$payment->id} anulado. Motivo: {$payment->void_reason}"
            );
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
