<?php

namespace App\Rules;

use App\Models\Subscription;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveSubscription implements ValidationRule
{
    private ?Subscription $subscription = null;

    /**
     * Validate that the subscription exists and is not expired.
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $this->subscription = Subscription::find($value);

        if (! $this->subscription) {
            $fail('La suscripción seleccionada no existe.');

            return;
        }

        if ($this->subscription->isExpired()) {
            $fail('No se puede registrar un pago en una suscripción vencida.');
        }
    }

    /**
     * Return the subscription resolved during validation.
     */
    public function subscription(): ?Subscription
    {
        return $this->subscription;
    }
}