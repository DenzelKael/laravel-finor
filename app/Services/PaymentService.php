<?php

namespace App\Services;

use App\DTOs\PaymentData;
use App\Enums\PaymentStatus;
use App\Events\PaymentRegistered;
use App\Exceptions\ExpiredSubscriptionException;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Registra un pago de forma atómica para una suscripción.
     *
     * @throws ExpiredSubscriptionException
     */
    public function registerPayment(
        Subscription $subscription,
        PaymentData $data
    ): Payment {
        if ($subscription->isExpired()) {
            throw new ExpiredSubscriptionException;
        }

        $payment = DB::transaction(
            function () use ($subscription, $data): Payment {
                return $subscription->payments()->create([
                    'amount' => $data->amount,
                    'payment_method' => $data->paymentMethod,
                    'payment_date' => $data->paymentDate->toDateString(),
                    'status' => PaymentStatus::Registered,
                ]);
            }
        );

        PaymentRegistered::dispatch($payment);

        return $payment;
    }

    /**
     * Anula un pago sin eliminar su registro contable.
     */
    public function voidPayment(
        Payment $payment,
        string $reason
    ): Payment {
        return DB::transaction(
            function () use ($payment, $reason): Payment {
                $payment->update([
                    'status' => PaymentStatus::Voided,
                    'void_reason' => $reason,
                    'voided_at' => now(),
                ]);

                return $payment->refresh();
            }
        );
    }
}
