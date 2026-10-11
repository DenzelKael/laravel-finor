<?php

namespace Tests\Feature;

use App\Models\Client;
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
        $client = Client::create(['name' => 'Original', 'email' => 'original@example.com']);

        $response = $this->putJson(route('clients.update', $client), [
            'name' => 'Nombre Actualizado',
            'email' => 'original@example.com',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.name', 'Nombre Actualizado');
    }

    public function test_destroy_removes_a_client(): void
    {
        $client = Client::create(['name' => 'A borrar', 'email' => 'borrar@example.com']);

        $response = $this->deleteJson(route('clients.destroy', $client));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
