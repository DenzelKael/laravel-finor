<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPaymentBalanceTest extends TestCase
{
    use RefreshDatabase;

    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $client = Client::create([
            'name' => 'Cliente Pagos Parciales',
            'email' => 'parciales@prueba.com',
            'phone' => '70000001',
        ]);

        $plan = Plan::create([
            'nombre' => 'Plan Test',
            'descripcion' => 'Plan para pruebas de saldo pendiente',
            'precio' => 500.00,
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

    public function test_subscription_calculates_paid_and_pending_amounts(): void
    {
        $this->subscription->payments()->create([
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Cash,
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Registered,
        ]);

        $this->assertSame(
            150.00,
            $this->subscription->paidAmount()
        );

        $this->assertSame(
            350.00,
            $this->subscription->pendingBalance()
        );

        $this->assertFalse(
            $this->subscription->isFullyPaid()
        );
    }

    public function test_multiple_partial_payments_reduce_pending_balance(): void
    {
        $this->subscription->payments()->create([
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Cash,
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Registered,
        ]);

        $this->subscription->payments()->create([
            'amount' => 200.00,
            'payment_method' => PaymentMethod::Card,
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Registered,
        ]);

        $this->assertSame(
            350.00,
            $this->subscription->paidAmount()
        );

        $this->assertSame(
            150.00,
            $this->subscription->pendingBalance()
        );
    }

    public function test_voided_payment_does_not_reduce_pending_balance(): void
    {
        $this->subscription->payments()->create([
            'amount' => 150.00,
            'payment_method' => PaymentMethod::Cash,
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Registered,
        ]);

        $this->subscription->payments()->create([
            'amount' => 200.00,
            'payment_method' => PaymentMethod::Transfer,
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Voided,
            'void_reason' => 'Pago anulado para prueba.',
            'voided_at' => now(),
        ]);

        $this->assertSame(
            150.00,
            $this->subscription->paidAmount()
        );

        $this->assertSame(
            350.00,
            $this->subscription->pendingBalance()
        );
    }

    public function test_subscription_is_fully_paid_when_balance_reaches_zero(): void
    {
        $this->subscription->payments()->create([
            'amount' => 500.00,
            'payment_method' => PaymentMethod::Qr,
            'payment_date' => now()->toDateString(),
            'status' => PaymentStatus::Registered,
        ]);

        $this->assertSame(
            500.00,
            $this->subscription->paidAmount()
        );

        $this->assertSame(
            0.00,
            $this->subscription->pendingBalance()
        );

        $this->assertTrue(
            $this->subscription->isFullyPaid()
        );
    }
}
