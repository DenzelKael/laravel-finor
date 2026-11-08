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
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'payment_method' => $this->faker->randomElement(
                PaymentMethod::cases()
            ),
            'payment_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'status' => PaymentStatus::Registered,
            'void_reason' => null,
            'voided_at' => null,
        ];
    }
}
