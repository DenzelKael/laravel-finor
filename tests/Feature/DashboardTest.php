<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private Plan $plan;

    private Subscription $anySubscription;

    protected function setUp(): void
    {
        parent::setUp();


        $this->travelTo(Carbon::parse('2026-10-15 10:00:00'));


        $this->actingAs(User::factory()->create());

        $this->plan = Plan::create([
            'nombre' => 'Plan Dashboard',
            'descripcion' => 'Plan para pruebas del dashboard',
            'precio' => 100.00,
            'duracion_dias' => 30,
            'activo' => true,
        ]);

        $this->seedSubscriptions();
        $this->seedPayments();
    }


    private function seedSubscriptions(): void
    {
        $this->suscripcion('A', 30);
        $this->suscripcion('B', 3);

        $c = Client::factory()->create();
        $this->crearSuscripcion($c, 20);
        $this->crearSuscripcion($c, 5);

        $this->suscripcion('G', 0);
        $this->suscripcion('H', 7);
        $this->suscripcion('I', 8);

        $this->suscripcion('D', -5, SubscriptionStatus::Expired);
        $this->suscripcion('E', 30, SubscriptionStatus::Cancelled);
        $this->suscripcion('F', -1, SubscriptionStatus::Active);
    }

    private function seedPayments(): void
    {
        $this->pago('100.00', '2026-10-01', PaymentStatus::Registered);
        $this->pago('250.50', '2026-10-31', PaymentStatus::Registered);

        $this->pago('999.00', '2026-10-10', $this->estadoAnulado());
        $this->pago('500.00', '2026-09-30', PaymentStatus::Registered);
        $this->pago('700.00', '2026-11-01', PaymentStatus::Registered);
    }

    public function test_dashboard_shows_the_three_indicators_with_known_data(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('clientesActivos', 6)
            ->assertViewHas('ingresosMes', 350.5)
            ->assertViewHas('suscripcionesPorVencer', 4);
    }

    public function test_active_client_is_counted_once_even_with_several_current_subscriptions(): void
    {

        $this->get(route('dashboard'))->assertViewHas('clientesActivos', 6);

        $this->assertSame(
            7,
            Subscription::active()->count(),
            'Hay 7 suscripciones vigentes, pero solo 6 clientes distintos.'
        );
    }

    public function test_client_with_only_expired_cancelled_or_lapsed_subscriptions_is_not_active(): void
    {
        $cliente = Client::factory()->create();
        $this->crearSuscripcion($cliente, -10, SubscriptionStatus::Expired);
        $this->crearSuscripcion($cliente, 30, SubscriptionStatus::Cancelled);
        $this->crearSuscripcion($cliente, -1, SubscriptionStatus::Active);


        $this->get(route('dashboard'))->assertViewHas('clientesActivos', 6);
    }

    public function test_monthly_revenue_is_displayed_in_bolivianos(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bs 350,50')
            ->assertDontSee('$350');
    }


    private function suscripcion(
        string $nombre,
        int $dias,
        SubscriptionStatus $status = SubscriptionStatus::Active
    ): Subscription {
        $cliente = Client::factory()->create(['name' => "Cliente {$nombre}"]);

        return $this->crearSuscripcion($cliente, $dias, $status);
    }

    private function crearSuscripcion(
        Client $cliente,
        int $dias,
        SubscriptionStatus $status = SubscriptionStatus::Active
    ): Subscription {
        $subscription = Subscription::create([
            'client_id' => $cliente->id,
            'plan_id' => $this->plan->id,
            'start_date' => today()->subDays(40)->toDateString(),
            'expiration_date' => today()->addDays($dias)->toDateString(),
            'status' => $status,
        ]);

        $this->anySubscription ??= $subscription;

        return $subscription;
    }

    private function pago(string $monto, string $fecha, PaymentStatus $status): Payment
    {
        return Payment::create([
            'subscription_id' => $this->anySubscription->id,
            'amount' => $monto,
            'payment_method' => PaymentMethod::cases()[0],
            'payment_date' => $fecha,
            'status' => $status,
        ]);
    }

    private function estadoAnulado(): PaymentStatus
    {
        return collect(PaymentStatus::cases())
            ->first(fn (PaymentStatus $caso) => $caso !== PaymentStatus::Registered);
    }
}
