<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::inRandomOrder()->value('id') ?? Client::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
            ])->id,
            'plan_id' => Plan::inRandomOrder()->value('id') ?? Plan::create([
                'nombre' => 'Plan Estándar',
                'descripcion' => 'Plan estándar con acceso completo',
                'precio' => 150.00,
                'duracion_dias' => 30,
                'activo' => true,
            ])->id,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(3)->endOfDay(),
            'status' => SubscriptionStatus::Active->value,
        ];
    }

    /**
     * Indicate that the subscription is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::Active->value,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(3)->endOfDay(),
        ]);
    }

    /**
     * Indicate that the subscription is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::Expired->value,
            'start_date' => now()->subMonths(4),
            'end_date' => now()->subDays(5)->endOfDay(),
        ]);
    }

    /**
     * Indicate that the subscription is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::Cancelled->value,
        ]);
    }
}
