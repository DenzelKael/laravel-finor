<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PaymentRegisteredEventTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $permission = Permission::findOrCreate(
            'payments.create',
            'web'
        );

        $this->user->givePermissionTo($permission);

        $client = Client::create([
            'name' => 'Cliente Evento',
            'email' => 'evento@prueba.com',
            'phone' => '70000002',
        ]);

        $plan = Plan::create([
            'nombre' => 'Plan Evento Test',
            'descripcion' => 'Plan para probar PaymentRegistered',
            'precio' => 150.00,
            'duracion_dias' => 30,
            'activo' => true,
        ]);

        $this->subscription = Subscription::factory()
            ->active()
            ->create([
                'client_id' => $client->id,
                'plan_id' => $plan->id,
            ]);
    }

    public function test_partial_payment_keeps_subscription_active(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/payments', [
                'subscription_id' => $this->subscription->id,
                'amount' => 50.00,
                'payment_method' => PaymentMethod::Cash->value,
                'payment_date' => now()->toDateString(),
            ]);

        $response->assertCreated();

        $this->subscription->refresh();

        $this->assertSame(
            SubscriptionStatus::Active,
            $this->subscription->status
        );

        $this->assertFalse(
            $this->subscription->isFullyPaid()
        );

        $this->assertSame(
            100.00,
            $this->subscription->pendingBalance()
        );
    }

    public function test_full_payment_keeps_subscription_active_and_marks_it_as_fully_paid(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/payments', [
                'subscription_id' => $this->subscription->id,
                'amount' => 150.00,
                'payment_method' => PaymentMethod::Qr->value,
                'payment_date' => now()->toDateString(),
            ]);

        $response->assertCreated();

        $this->subscription->refresh();

        $this->assertSame(
            SubscriptionStatus::Active,
            $this->subscription->status
        );

        $this->assertTrue(
            $this->subscription->isFullyPaid()
        );

        $this->assertSame(
            0.00,
            $this->subscription->pendingBalance()
        );

        $this->assertTrue(
            Subscription::active()
                ->whereKey($this->subscription->id)
                ->exists()
        );
    }
    public function test_full_payment_does_not_allow_another_active_subscription_for_same_client(): void
{
    $subscriptionPermission = Permission::findOrCreate(
        'subscriptions.create',
        'web'
    );

    $this->user->givePermissionTo(
        $subscriptionPermission
    );

    // Pagar completamente la suscripción existente.
    $paymentResponse = $this->actingAs($this->user)
        ->postJson('/payments', [
            'subscription_id' => $this->subscription->id,
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Cash->value,
            'payment_date' => now()->toDateString(),
        ]);

    $paymentResponse->assertCreated();

    $this->subscription->refresh();

    // El pago completo NO modifica el ciclo de vida.
    $this->assertSame(
        SubscriptionStatus::Active,
        $this->subscription->status
    );

    $this->assertTrue(
        $this->subscription->isFullyPaid()
    );

    $this->assertSame(
        0.00,
        $this->subscription->pendingBalance()
    );

    // La suscripción sigue siendo considerada activa.
    $this->assertTrue(
        Subscription::active()
            ->whereKey($this->subscription->id)
            ->exists()
    );

    // Intentar crear una segunda suscripción
    // para el mismo cliente.
    $secondSubscriptionResponse = $this
        ->actingAs($this->user)
        ->postJson('/subscriptions', [
            'client_id' => $this->subscription->client_id,
            'plan_id' => $this->subscription->plan_id,
            'start_date' => today()->toDateString(),
        ]);

    // Debe ser rechazada porque el cliente
    // todavía posee una suscripción ACTIVE.
    $secondSubscriptionResponse
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'client_id',
        ]);

    // Debe continuar existiendo una sola
    // suscripción activa para ese cliente.
    $this->assertSame(
        1,
        Subscription::where(
            'client_id',
            $this->subscription->client_id
        )
            ->active()
            ->count()
    );
}
}