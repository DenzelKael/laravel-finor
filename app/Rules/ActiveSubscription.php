<?php

namespace App\Rules;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveSubscription implements ValidationRule
{
    private ?Subscription $subscription = null;

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

            return;
        }

        if ($this->subscription->status !== SubscriptionStatus::Active) {
            $fail('La suscripción seleccionada no se encuentra activa.');
        }
    }

    public function subscription(): ?Subscription
    {
        return $this->subscription;
    }
}
