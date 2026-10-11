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
        return $user->can('payments.view');
    }

    /**
     * Determine whether the user can view a payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->can('payments.view');
    }

    /**
     * Determine whether the user can create payments.
     */
    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    /**
     * Determine whether the user can cancel a payment.
     */
    public function cancel(User $user, Payment $payment): bool
    {
        return $user->can('payments.cancel')
            && $payment->status !== PaymentStatus::Cancelled;
    }
}