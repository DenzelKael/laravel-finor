<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PaymentHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Client $clientA;

    private Client $clientB;

    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $viewPermission = Permission::findOrCreate(
            'payments.view',
            'web'
        );

        $this->user->givePermissionTo($viewPermission);

        $this->clientA = Client::create([
            'name' => 'Cliente A',
            'email' => 'cliente.a@prueba.com',
            'phone' => '70000010',
        ]);

        $this->clientB = Client::create([
            'name' => 'Cliente B',
            'email' => 'cliente.b@prueba.com',
            'phone' => '70000011',
        ]);

        $this->plan = Plan::create([
            'nombre' => 'Plan Historial',
            'descripcion' => 'Plan para pruebas de historial',
            'precio' => 500.00,
            'duracion_dias' => 30,
            'activo' => true,
        ]);
    }

    public function test_history_by_subscription_only_contains_its_payments(): void
    {
        $subscriptionA = $this->createSubscription($this->clientA);
        $subscriptionB = $this->createSubscription($this->clientB);

        $paymentA = $this->createPayment(
            $subscriptionA,
            100.00
        );

        $paymentB = $this->createPayment(
            $subscriptionB,
            200.00
        );

        $response = $this->actingAs($this->user)
            ->get(
                route(
                    'payments.history.subscription',
                    $subscriptionA
                )
            );

        $response->assertOk()
            ->assertViewIs('payments.history')
            ->assertViewHas('payments', function ($payments) use (
                $paymentA,
                $paymentB
            ): bool {
                $ids = $payments
                    ->getCollection()
                    ->pluck('id');

                return $ids->contains($paymentA->id)
                    && ! $ids->contains($paymentB->id);
            });
    }

    public function test_history_by_client_contains_payments_from_all_its_subscriptions(): void
    {
        $subscriptionA1 = $this->createSubscription($this->clientA);
        $subscriptionA2 = $this->createSubscription($this->clientA);
        $subscriptionB = $this->createSubscription($this->clientB);

        $paymentA1 = $this->createPayment(
            $subscriptionA1,
            100.00
        );

        $paymentA2 = $this->createPayment(
            $subscriptionA2,
            150.00
        );

        $paymentB = $this->createPayment(
            $subscriptionB,
            200.00
        );

        $response = $this->actingAs($this->user)
            ->get(
                route(
                    'payments.history.client',
                    $this->clientA
                )
            );

        $response->assertOk()
            ->assertViewIs('payments.history')
            ->assertViewHas('payments', function ($payments) use (
                $paymentA1,
                $paymentA2,
                $paymentB
            ): bool {
                $ids = $payments
                    ->getCollection()
                    ->pluck('id');

                return $ids->contains($paymentA1->id)
                    && $ids->contains($paymentA2->id)
                    && ! $ids->contains($paymentB->id);
            });
    }

    private function createSubscription(
        Client $client
    ): Subscription {
        return Subscription::factory()
            ->active()
            ->create([
                'client_id' => $client->id,
                'plan_id' => $this->plan->id,
            ]);
    }

    private function createPayment(
        Subscription $subscription,
        float $amount
    ): Payment {
        return $subscription
            ->payments()
            ->create([
                'amount' => $amount,
                'payment_method' => PaymentMethod::Cash,
                'payment_date' => now()->toDateString(),
                'status' => PaymentStatus::Registered,
            ]);
    }
}