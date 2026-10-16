<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'clients.view']);
        Permission::firstOrCreate(['name' => 'clients.create']);
        Permission::firstOrCreate(['name' => 'clients.update']);
        Permission::firstOrCreate(['name' => 'clients.delete']);

        $user = User::factory()->create();
        $user->givePermissionTo(['clients.view', 'clients.create', 'clients.update', 'clients.delete']);

        $this->actingAs($user);
    }

    public function test_store_creates_a_client(): void
    {
        $response = $this->postJson(route('clients.store'), [
            'name' => 'Carlos Ferrel',
            'email' => 'carlos@example.com',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.email', 'carlos@example.com');
    }

    public function test_store_fails_validation(): void
    {
        $response = $this->postJson(route('clients.store'), ['name' => '', 'email' => 'not-an-email']);

        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_update_with_same_email_succeeds(): void
    {
        $client = Client::factory()->create(['email' => 'original@example.com']);

        $response = $this->putJson(route('clients.update', $client), [
            'name' => 'Nombre Actualizado',
            'email' => 'original@example.com',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.name', 'Nombre Actualizado');
    }

    public function test_destroy_removes_a_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson(route('clients.destroy', $client));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_destroy_fails_with_422_when_client_has_subscriptions(): void
    {
        $client = Client::factory()->create();

        Subscription::create([
            'client_id' => $client->id,
            'plan_id' => Plan::factory()->create()->id,
            'start_date' => today(),
            'expiration_date' => today()->addMonth(),
            'status' => SubscriptionStatus::Active,
        ]);

        $this->deleteJson(route('clients.destroy', $client))
            ->assertStatus(422)
            ->assertJsonPath('message', 'No se puede eliminar un cliente con suscripciones.');

        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    public function test_update_and_destroy_return_404_for_missing_client(): void
    {
        $this->putJson(route('clients.update', 999999), [
            'name' => 'Nadie',
            'email' => 'nadie@example.com',
        ])->assertStatus(404);

        $this->deleteJson(route('clients.destroy', 999999))->assertStatus(404);
    }

    public function test_user_without_permission_gets_403(): void
    {
        $client = Client::factory()->create();
        $sinPermiso = User::factory()->create();

        $this->actingAs($sinPermiso)
            ->postJson(route('clients.store'), ['name' => 'X', 'email' => 'x@example.com'])
            ->assertStatus(403);

        $this->actingAs($sinPermiso)
            ->deleteJson(route('clients.destroy', $client))
            ->assertStatus(403);
    }
}
