<?php

namespace App\Rules;

use App\Models\Subscription;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveSubscription implements ValidationRule
{
    /**
     * Validate that the subscription exists and is not expired.
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $subscription = Subscription::find($value);

        if (! $subscription) {
            $fail('La suscripción seleccionada no existe.');

            return;
        }

        if ($subscription->isExpired()) {
            $fail('No se puede registrar un pago en una suscripción vencida.');
        }
    }
}