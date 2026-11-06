<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMonthlyTotalTest extends TestCase
{
    use RefreshDatabase;

    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $client = Client::create([
            'name' => 'Cliente Dashboard',
            'email' => 'dashboard@prueba.com',
            'phone' => '70000020',
        ]);

        $plan = Plan::create([
            'nombre' => 'Plan Dashboard',
            'descripcion' => 'Plan para pruebas del dashboard',
            'precio' => 2000.00,
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

    public function test_total_of_month_only_includes_registered_payments_from_current_month(): void
    {
        $this->createPayment(
            100.00,
            now()->toDateString(),
            PaymentStatus::Registered
        );

        $this->createPayment(
            250.00,
            now()->toDateString(),
            PaymentStatus::Registered
        );

        $this->createPayment(
            300.00,
            now()->toDateString(),
            PaymentStatus::Voided
        );

        $this->createPayment(
            500.00,
            now()
                ->subMonthNoOverflow()
                ->startOfMonth()
                ->toDateString(),
            PaymentStatus::Registered
        );

        $total = Payment::totalOfMonth()
            ->sum('amount');

        $this->assertEquals(
            350.00,
            (float) $total
        );
    }

    private function createPayment(
        float $amount,
        string $paymentDate,
        PaymentStatus $status
    ): Payment {
        return $this->subscription
            ->payments()
            ->create([
                'amount' => $amount,
                'payment_method' => PaymentMethod::Cash,
                'payment_date' => $paymentDate,
                'status' => $status,
                'void_reason' => $status === PaymentStatus::Voided
                    ? 'Pago anulado para prueba.'
                    : null,
                'voided_at' => $status === PaymentStatus::Voided
                    ? now()
                    : null,
            ]);
    }
}
