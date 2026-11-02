<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'amount' => fake()->randomFloat(2, 50, 500),
            'payment_method' => PaymentMethod::cases()[0],
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Registered,
        ];
    }

    public function voided(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Voided,
            'void_reason' => 'Error de monto',
            'voided_at' => now(),
        ]);
    }
}
