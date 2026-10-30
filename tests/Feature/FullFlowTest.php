<?php

namespace Tests\Feature;

use App\Enums\ClientStatus;
use App\Enums\PaymentMethod;
use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->user = User::factory()->create();

        $this->user->givePermissionTo([
            'clients.view',
            'clients.create',
            'plans.view',
            'plans.create',
            'subscriptions.view',
            'subscriptions.create',
            'payments.view',
            'payments.create',
        ]);

        $this->actingAs($this->user);
    }

    public function test_complete_flow_from_client_to_dashboard(): void
    {
        /*
         * 1. CLIENTES
         */
        $clientPayload = Client::factory()
            ->make([
                'status' => ClientStatus::Active,
            ])
            ->toArray();

        $clientResponse = $this->postJson(
            route('api.v1.clients.store'),
            $clientPayload
        );

        $this->assertSame(
            201,
            $clientResponse->status(),
            '[CLIENTES] No se pudo crear el cliente.'
        );

        $clientId = $clientResponse->json('data.id');

        $this->assertNotNull(
            $clientId,
            '[CLIENTES] La respuesta no devolvió el ID del cliente.'
        );

        $this->assertDatabaseHas('clients', [
            'id' => $clientId,
            'status' => ClientStatus::Active->value,
        ]);

        /*
         * 2. PLANES
         *
         * Se utilizan 5 días para que la suscripción
         * quede dentro del rango "por vencer" del dashboard.
         */
        $planPayload = Plan::factory()
            ->make([
                'precio' => 100.00,
                'duracion_dias' => 5,
                'activo' => true,
            ])
            ->toArray();

        $planResponse = $this->postJson(
            route('api.v1.plans.store'),
            $planPayload
        );

        $this->assertSame(
            201,
            $planResponse->status(),
            '[PLANES] No se pudo crear el plan activo.'
        );

        $planId = $planResponse->json('data.id');

        $this->assertNotNull(
            $planId,
            '[PLANES] La respuesta no devolvió el ID del plan.'
        );

        $this->assertDatabaseHas('plans', [
            'id' => $planId,
            'activo' => true,
        ]);

        /*
         * 3. SUSCRIPCIONES
         */
        $subscriptionResponse = $this->postJson(
            route('subscriptions.store'),
            [
                'client_id' => $clientId,
                'plan_id' => $planId,
                'start_date' => today()->toDateString(),
            ]
        );

        $this->assertSame(
            201,
            $subscriptionResponse->status(),
            '[SUSCRIPCIONES] No se pudo crear la suscripción.'
        );

        $subscriptionId = $subscriptionResponse->json('data.id');

        $this->assertNotNull(
            $subscriptionId,
            '[SUSCRIPCIONES] La respuesta no devolvió el ID de la suscripción.'
        );

        $expectedExpiration = today()
            ->copy()
            ->addDays(5)
            ->toDateString();

        $subscriptionResponse
            ->assertJsonPath(
                'data.status',
                SubscriptionStatus::Active->value
            )
            ->assertJsonPath(
                'data.expiration_date',
                $expectedExpiration
            );

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscriptionId,
            'client_id' => $clientId,
            'plan_id' => $planId,
            'status' => SubscriptionStatus::Active->value,
        ]);

        /*
         * 4. PAGOS
         *
         * Pago parcial:
         * plan = Bs 100
         * pago = Bs 40
         * saldo esperado = Bs 60
         */
        $paymentMethod = PaymentMethod::cases()[0]->value;

        $paymentResponse = $this->postJson(
            route('api.v1.payments.store'),
            [
                'subscription_id' => $subscriptionId,
                'amount' => 40.00,
                'payment_method' => $paymentMethod,
                'payment_date' => today()->toDateString(),
            ]
        );

        $this->assertSame(
            201,
            $paymentResponse->status(),
            '[PAGOS] No se pudo registrar el pago.'
        );

        $paymentId = $paymentResponse->json('data.id');

        $this->assertNotNull(
            $paymentId,
            '[PAGOS] La respuesta no devolvió el ID del pago.'
        );

        $this->assertDatabaseHas('payments', [
            'id' => $paymentId,
            'subscription_id' => $subscriptionId,
        ]);

        /*
         * 5. SALDO Y ESTADO
         */
        $subscription = Subscription::findOrFail(
            $subscriptionId
        );

        $this->assertEquals(
            60.00,
            $subscription->pendingBalance(),
            '[PAGOS] El saldo pendiente debería ser Bs 60.'
        );

        $this->assertFalse(
            $subscription->isFullyPaid(),
            '[PAGOS] La suscripción no debería estar totalmente pagada.'
        );

        $this->assertSame(
            SubscriptionStatus::Active,
            $subscription->status,
            '[SUSCRIPCIONES] Un pago parcial debe mantener la suscripción ACTIVE.'
        );

        /*
         * 6. DASHBOARD
         */
        $dashboardResponse = $this->get(
            route('dashboard')
        );

        $this->assertSame(
            200,
            $dashboardResponse->status(),
            '[DASHBOARD] No se pudo cargar el dashboard.'
        );

        $this->assertSame(
            1,
            (int) $dashboardResponse->viewData('clientesActivos'),
            '[DASHBOARD] El indicador de clientes activos es incorrecto.'
        );

        $this->assertEquals(
            40.00,
            (float) $dashboardResponse->viewData('ingresosMes'),
            '[DASHBOARD] El indicador de ingresos del mes es incorrecto.'
        );

        $this->assertSame(
            1,
            (int) $dashboardResponse->viewData('suscripcionesPorVencer'),
            '[DASHBOARD] El indicador de suscripciones por vencer es incorrecto.'
        );
    }
}
