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

        $this->assertSame(
            100.00,
            $this->subscription->pendingBalance()
        );
    }

    public function test_full_payment_marks_subscription_as_paid(): void
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
            SubscriptionStatus::Paid,
            $this->subscription->status
        );

        $this->assertSame(
            0.00,
            $this->subscription->pendingBalance()
        );
    }
    public function test_paid_subscription_is_still_active_while_not_expired(): void
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
        SubscriptionStatus::Paid,
        $this->subscription->status
    );

    $this->assertTrue(
        Subscription::active()
            ->whereKey($this->subscription->id)
            ->exists()
    );
}
}