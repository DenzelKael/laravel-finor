<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpiringSubscriptionsReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function authorizedUser(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo('subscriptions.view');

        return $user;
    }

    private function unauthorizedUser(): User
    {
        return User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('reports.subscriptions.expiring'));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_report(): void
    {
        $response = $this->actingAs($this->unauthorizedUser())
            ->get(route('reports.subscriptions.expiring'));

        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_report_view(): void
    {
        $response = $this->actingAs($this->authorizedUser())
            ->get(route('reports.subscriptions.expiring'));

        $response->assertOk();
        $response->assertViewIs('reports.expiring_subscriptions');
        $response->assertViewHasAll(['subscriptions', 'counts', 'days', 'search']);
    }

    public function test_report_filters_by_7_15_and_30_days_correctly(): void
    {
        $user = $this->authorizedUser();
        $client = Client::factory()->create();
        $plan = Plan::factory()->create(['precio' => 100]);

        // 1. Vence en 5 días (dentro de 7, 15 y 30)
        $subIn5Days = Subscription::factory()->create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => today()->addDays(5),
        ]);

        // 2. Vence en 12 días (fuera de 7, dentro de 15 y 30)
        $subIn12Days = Subscription::factory()->create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => today()->addDays(12),
        ]);

        // 3. Vence en 25 días (fuera de 7 y 15, dentro de 30)
        $subIn25Days = Subscription::factory()->create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => today()->addDays(25),
        ]);

        // 4. Vence en 50 días (fuera de todos)
        $subIn50Days = Subscription::factory()->create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => today()->addDays(50),
        ]);

        // 5. Vencida en el pasado (no debe aparecer en ninguna)
        $subExpired = Subscription::factory()->create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Expired->value,
            'expiration_date' => today()->subDays(5),
        ]);

        // Prueba filtro a 7 días
        $res7 = $this->actingAs($user)->get(route('reports.subscriptions.expiring', ['days' => 7]));
        $res7->assertOk();
        $res7Items = $res7->viewData('subscriptions')->items();
        $res7Ids = collect($res7Items)->pluck('id')->all();

        $this->assertContains($subIn5Days->id, $res7Ids);
        $this->assertNotContains($subIn12Days->id, $res7Ids);
        $this->assertNotContains($subIn25Days->id, $res7Ids);
        $this->assertNotContains($subIn50Days->id, $res7Ids);
        $this->assertNotContains($subExpired->id, $res7Ids);

        // Prueba filtro a 15 días
        $res15 = $this->actingAs($user)->get(route('reports.subscriptions.expiring', ['days' => 15]));
        $res15->assertOk();
        $res15Items = $res15->viewData('subscriptions')->items();
        $res15Ids = collect($res15Items)->pluck('id')->all();

        $this->assertContains($subIn5Days->id, $res15Ids);
        $this->assertContains($subIn12Days->id, $res15Ids);
        $this->assertNotContains($subIn25Days->id, $res15Ids);
        $this->assertNotContains($subIn50Days->id, $res15Ids);
        $this->assertNotContains($subExpired->id, $res15Ids);

        // Prueba filtro a 30 días
        $res30 = $this->actingAs($user)->get(route('reports.subscriptions.expiring', ['days' => 30]));
        $res30->assertOk();
        $res30Items = $res30->viewData('subscriptions')->items();
        $res30Ids = collect($res30Items)->pluck('id')->all();

        $this->assertContains($subIn5Days->id, $res30Ids);
        $this->assertContains($subIn12Days->id, $res30Ids);
        $this->assertContains($subIn25Days->id, $res30Ids);
        $this->assertNotContains($subIn50Days->id, $res30Ids);
        $this->assertNotContains($subExpired->id, $res30Ids);
    }

    public function test_report_search_filters_by_client_name(): void
    {
        $user = $this->authorizedUser();
        $clientTarget = Client::factory()->create(['name' => 'Alejandro Morales']);
        $clientOther = Client::factory()->create(['name' => 'Beatriz Santos']);
        $plan = Plan::factory()->create(['precio' => 100]);

        $subTarget = Subscription::factory()->create([
            'client_id' => $clientTarget->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => today()->addDays(5),
        ]);

        $subOther = Subscription::factory()->create([
            'client_id' => $clientOther->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'expiration_date' => today()->addDays(5),
        ]);

        $response = $this->actingAs($user)->get(route('reports.subscriptions.expiring', [
            'days' => 15,
            'search' => 'Morales',
        ]));

        $response->assertOk();
        $items = $response->viewData('subscriptions')->items();
        $ids = collect($items)->pluck('id')->all();

        $this->assertContains($subTarget->id, $ids);
        $this->assertNotContains($subOther->id, $ids);
    }

    public function test_report_returns_json_when_requested(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->getJson(route('reports.subscriptions.expiring', ['days' => 15]));

        $response->assertOk();
        $response->assertJsonStructure([
            'subscriptions' => ['data', 'total'],
            'counts' => ['7', '15', '30'],
            'days',
        ]);
    }
}
