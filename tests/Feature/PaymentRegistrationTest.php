<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Subscription $activeSubscription;

    private Subscription $expiredSubscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $client = Client::create([
            'name' => 'Cliente de Prueba',
            'email' => 'cliente@prueba.com',
            'phone' => '70000000',
        ]);

        $plan = Plan::create([
            'nombre' => 'Plan Mensual Test',
            'precio' => 150.00,
            'duracion_dias' => 30,
        ]);

        $this->activeSubscription = Subscription::factory()
            ->active()
            ->create([
                'client_id' => $client->id,
                'plan_id' => $plan->id,
            ]);

        $this->expiredSubscription = Subscription::factory()
            ->expired()
            ->create([
                'client_id' => $client->id,
                'plan_id' => $plan->id,
            ]);
    }

    /**
     * Test 1: A valid payment is registered successfully.
     */
    public function test_valid_payment_is_registered_successfully(): void
    {
        $payload = [
            'subscription_id' => $this->activeSubscription->id,
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Cash->value,
            'payment_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/payments', $payload);

        $response->assertStatus(201)
            ->assertJsonPath(
                'data.status',
                PaymentStatus::Registered->value
            );

        $this->assertDatabaseHas('payments', [
            'subscription_id' => $this->activeSubscription->id,
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Cash->value,
            'status' => PaymentStatus::Registered->value,
        ]);
    }

    /**
     * Test 2: Amounts less than or equal to zero are rejected.
     */
    public function test_payment_fails_when_amount_is_less_than_or_equal_to_zero(): void
    {
        $payload = [
            'subscription_id' => $this->activeSubscription->id,
            'amount' => 0.00,
            'payment_method' => PaymentMethod::Card->value,
            'payment_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/payments', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'amount',
            ]);
    }

    /**
     * Test 3: Payments for expired subscriptions are rejected.
     */
    public function test_payment_fails_when_subscription_is_expired(): void
    {
        $payload = [
            'subscription_id' => $this->expiredSubscription->id,
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Transfer->value,
            'payment_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/payments', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'subscription_id',
            ]);
    }
}