<?php

namespace App\Policies;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determina si el usuario puede ver el listado de pagos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('payments.viewAny') || $user->can('payments.index');
    }

    /**
     * Determina si el usuario puede ver el detalle o recibo de un pago.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->can('payments.view');
    }

    /**
     * Determina si el usuario puede registrar pagos.
     */
    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    /**
     * Determina si el usuario puede anular un pago.
     */
    public function cancel(User $user, Payment $payment): bool
    {
        return $payment->status !== PaymentStatus::Cancelled
            && $user->can('payments.cancel');
    }
}