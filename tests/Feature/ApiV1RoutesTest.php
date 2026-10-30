<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1RoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function userWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();

        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_guest_cannot_access_api_v1(): void
    {
        $this->getJson(route('api.v1.clients.index'))
            ->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_access_api_v1(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('api.v1.clients.index'))
            ->assertForbidden();
    }

    public function test_authorized_user_can_list_clients_as_json(): void
    {
        $user = $this->userWithPermissions([
            'clients.view',
        ]);

        Client::factory()->count(2)->create();

        $this->actingAs($user)
            ->getJson(route('api.v1.clients.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_authorized_user_can_list_plans_as_json(): void
    {
        $user = $this->userWithPermissions([
            'plans.view',
        ]);

        Plan::factory()->count(2)->create();

        $this->actingAs($user)
            ->getJson(route('api.v1.plans.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_authorized_user_can_list_payments_as_json(): void
    {
        $user = $this->userWithPermissions([
            'payments.view',
        ]);

        $this->actingAs($user)
            ->getJson(route('api.v1.payments.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_api_v1_returns_404_for_missing_client(): void
    {
        $user = $this->userWithPermissions([
            'clients.view',
        ]);

        $this->actingAs($user)
            ->getJson('/api/v1/clients/999999')
            ->assertNotFound();
    }

    public function test_api_v1_can_create_client_with_201(): void
    {
        $user = $this->userWithPermissions([
            'clients.create',
        ]);

        $client = Client::factory()->make();

        $response = $this->actingAs($user)
            ->postJson(
                route('api.v1.clients.store'),
                $client->toArray()
            );

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data',
                'message',
            ]);
    }

    public function test_api_v1_can_delete_client_with_204(): void
    {
        $user = $this->userWithPermissions([
            'clients.delete',
        ]);

        $client = Client::factory()->create();

        $this->actingAs($user)
            ->deleteJson(
                route('api.v1.clients.destroy', $client)
            )
            ->assertNoContent();

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_api_v1_can_create_plan_with_201(): void
    {
        $user = $this->userWithPermissions([
            'plans.create',
        ]);

        $plan = Plan::factory()->make();

        $response = $this->actingAs($user)
            ->postJson(
                route('api.v1.plans.store'),
                $plan->toArray()
            );

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    public function test_api_v1_can_delete_plan_with_204(): void
    {
        $user = $this->userWithPermissions([
            'plans.delete',
        ]);

        $plan = Plan::factory()->create();

        $this->actingAs($user)
            ->deleteJson(
                route('api.v1.plans.destroy', $plan)
            )
            ->assertNoContent();

        $this->assertDatabaseMissing('plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_api_v1_returns_422_for_invalid_client_data(): void
    {
        $user = $this->userWithPermissions([
            'clients.create',
        ]);

        $this->actingAs($user)
            ->postJson(
                route('api.v1.clients.store'),
                []
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
            ]);
    }
}
