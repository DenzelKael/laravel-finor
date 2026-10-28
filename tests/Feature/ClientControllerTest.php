<?php

namespace Tests\Feature;

use App\Enums\ClientStatus;
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

    public function test_store_defaults_status_to_active(): void
    {
        $this->postJson(route('clients.store'), [
            'name' => 'Sin Estado',
            'email' => 'sinestado@example.com',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.status_label', 'Activo');
    }

    public function test_store_rejects_an_invalid_status(): void
    {
        $this->postJson(route('clients.store'), [
            'name' => 'Estado Raro',
            'email' => 'raro@example.com',
            'status' => 'otro',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
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

    public function test_index_filters_by_search_and_status_without_breaking_the_or(): void
    {
        Client::factory()->create(['name' => 'Ana Activa', 'email' => 'activa@example.com']);
        Client::factory()->create(['name' => 'Ana Inactiva', 'email' => 'inactiva@example.com', 'status' => ClientStatus::Inactive]);
        Client::factory()->create(['name' => 'Zoe', 'email' => 'zoe@example.com']);

        $this->getJson(route('clients.index', ['q' => 'Ana']))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson(route('clients.index', ['q' => 'Ana', 'status' => 'active']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Ana Activa')
            ->assertJsonPath('data.0.status', 'active');
    }

    public function test_index_paginates_ten_per_page(): void
    {
        Client::factory()->count(12)->create();

        $this->getJson(route('clients.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 12);
    }

    public function test_index_rejects_an_invalid_status(): void
    {
        $this->getJson(route('clients.index', ['status' => 'otro']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_export_returns_a_csv_with_the_applied_filters(): void
    {
        Client::factory()->create(['email' => 'vigente@example.com']);
        Client::factory()->create(['email' => 'baja@example.com', 'status' => ClientStatus::Inactive]);

        $response = $this->get(route('clients.export', ['status' => 'inactive']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        $csv = $response->streamedContent();
        $this->assertStringContainsString('baja@example.com', $csv);
        $this->assertStringNotContainsString('vigente@example.com', $csv);
    }

    public function test_user_without_permission_cannot_list_or_export(): void
    {
        $sinPermiso = User::factory()->create();

        $this->actingAs($sinPermiso)->getJson(route('clients.index'))->assertStatus(403);
        $this->actingAs($sinPermiso)->get(route('clients.export'))->assertStatus(403);
    }

    public function test_show_displays_the_client_and_their_subscriptions(): void
    {
        $client = Client::factory()->create(['name' => 'Cliente Detalle']);

        Subscription::create([
            'client_id' => $client->id,
            'plan_id' => Plan::factory()->create(['nombre' => 'Plan de Prueba'])->id,
            'start_date' => today(),
            'expiration_date' => today()->addMonth(),
            'status' => SubscriptionStatus::Active,
        ]);

        $this->get(route('clients.show', $client))
            ->assertOk()
            ->assertSee('Cliente Detalle')
            ->assertSee('Plan de Prueba');
    }

    public function test_show_returns_404_for_missing_client(): void
    {
        $this->get(route('clients.show', 999999))->assertStatus(404);
    }
}
