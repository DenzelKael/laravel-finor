<?php

namespace App\Services;

use App\DTOs\PaymentData;
use App\Enums\PaymentStatus;
use App\Exceptions\ExpiredSubscriptionException;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Register a payment atomically for a subscription.
     *
     * @throws ExpiredSubscriptionException
     */
    public function registerPayment(
        Subscription $subscription,
        PaymentData $data
    ): Payment {
        if ($subscription->isExpired()) {
            throw new ExpiredSubscriptionException();
        }

        return DB::transaction(function () use ($subscription, $data): Payment {
            /** @var Payment $payment */
            $payment = $subscription->payments()->create([
                'amount' => $data->amount,
                'payment_method' => $data->paymentMethod,
                'payment_date' => $data->paymentDate->toDateString(),
                'status' => PaymentStatus::Registered,
            ]);

            return $payment;
        });
    }
}