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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_correct_stats()
    {
        $user = User::factory()->create();

        $client = Client::factory()->create();
        $plan = Plan::factory()->create(['precio' => 100]);

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => now()->addDays(10),
        ]);

        Payment::forceCreate([
            'subscription_id' => $subscription->id,
            'amount' => 100,
            'payment_date' => now(),
            'status' => PaymentStatus::Registered->value,
            'payment_method' => PaymentMethod::Cash->value,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('activeClients', 1);
        $response->assertViewHas('monthlyRevenue', 100);
        $response->assertViewHas('expiringSubscriptions', 1);
    }

    public function test_chart_data_returns_json_with_labels_and_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('dashboard.chart'));

        $response->assertOk();
        $response->assertJsonStructure(['labels', 'data']);
    }
}
