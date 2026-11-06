<?php

namespace Tests\Unit;

use App\Enums\SubscriptionStatus;
use App\Exceptions\SubscriptionAlreadyActiveException;
use App\Exceptions\SubscriptionCancelledException;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionService $service;

    private Client $client;

    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-01-10'));

        $this->service = new SubscriptionService;

        $this->client = Client::factory()->create();

        $this->plan = Plan::factory()->create([
            'nombre' => 'Plan de prueba',
            'precio' => 100,
            'duracion_dias' => 30,
            'activo' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_creates_subscription_and_calculates_expiration_date(): void
    {
        $subscription = $this->service->createSubscription([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'start_date' => '2026-01-10',
        ]);

        $this->assertEquals('2026-01-10', $subscription->start_date->toDateString());
        $this->assertEquals('2026-02-09', $subscription->expiration_date->toDateString());
        $this->assertEquals(SubscriptionStatus::Active, $subscription->status);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'expiration_date' => '2026-02-09 00:00:00',
        ]);
    }

    public function test_it_throws_exception_when_client_already_has_active_subscription(): void
    {
        Subscription::factory()->create([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'status' => SubscriptionStatus::Active,
            'expiration_date' => '2026-02-09',
        ]);

        $this->expectException(SubscriptionAlreadyActiveException::class);

        $this->service->createSubscription([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'start_date' => '2026-01-10',
        ]);
    }

    public function test_it_renews_active_subscription_from_expiration_date(): void
    {
        $subscription = Subscription::factory()->create([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'start_date' => '2025-12-20',
            'expiration_date' => '2026-01-20',
            'status' => SubscriptionStatus::Active,
        ]);

        $renewed = $this->service->renew($subscription);

        $this->assertEquals('2026-02-19', $renewed->expiration_date->toDateString());
        $this->assertEquals(SubscriptionStatus::Active, $renewed->status);
    }

    public function test_it_renews_expired_subscription_from_today(): void
    {
        $subscription = Subscription::factory()->create([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'start_date' => '2025-12-01',
            'expiration_date' => '2026-01-05',
            'status' => SubscriptionStatus::Expired,
        ]);

        $renewed = $this->service->renew($subscription);

        $this->assertEquals('2026-02-09', $renewed->expiration_date->toDateString());
        $this->assertEquals(SubscriptionStatus::Active, $renewed->status);
    }

    public function test_it_cannot_renew_cancelled_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'status' => SubscriptionStatus::Cancelled,
        ]);

        $this->expectException(SubscriptionCancelledException::class);
        $this->expectExceptionMessage('No se puede renovar una suscripción cancelada.');

        $this->service->renew($subscription);
    }

    public function test_it_cannot_cancel_already_cancelled_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
            'status' => SubscriptionStatus::Cancelled,
        ]);

        $this->expectException(SubscriptionCancelledException::class);
        $this->expectExceptionMessage('La suscripción ya se encuentra cancelada.');

        $this->service->cancel($subscription);
    }
}
