<?php

namespace App\Policies;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view the payment list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the payment details and receipt.
     */
    public function view(User $user, Payment $payment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create payments.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can cancel the payment.
     */
    public function cancel(User $user, Payment $payment): bool
    {
        return $payment->status !== PaymentStatus::Cancelled;
    }
}