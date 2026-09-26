<?php

namespace App\Services;

use App\Exceptions\ExpiredSubscriptionException;
use App\Models\Payment;
use App\Models\Subscription;

class PaymentService
{
    /**
     * Register a payment after verifying the subscription status.
     *
     * @throws ExpiredSubscriptionException
     */
    public function registerPayment(array $data): Payment
    {
        $subscription = Subscription::findOrFail($data['subscription_id']);

        if ($subscription->isExpired()) {
            throw new ExpiredSubscriptionException();
        }

        $data['status'] = 'REGISTERED';

        return Payment::create($data);
    }
}
