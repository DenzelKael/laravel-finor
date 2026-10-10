<?php

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');

    $this->client = Client::create([
        'name' => 'Cliente de prueba',
        'email' => 'cliente-' . uniqid() . '@example.com',
        'phone' => null,
        'address' => null,
    ]);

    $this->plan = Plan::factory()->create([
        'duracion_dias' => 30,
        'activo' => true,
    ]);
});

test('admin puede registrar una suscripcion correctamente', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/subscriptions', [
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
        ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'Suscripción registrada correctamente.')
        ->assertJsonPath('data.client_id', $this->client->id)
        ->assertJsonPath('data.plan_id', $this->plan->id);

    $this->assertDatabaseHas('subscriptions', [
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => SubscriptionStatus::Active->value,
    ]);
});

test('alta devuelve 422 cuando faltan datos obligatorios', function () {
    $this->actingAs($this->admin)
        ->postJson('/subscriptions', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['client_id', 'plan_id']);
});

test('no permite contratar un plan inactivo', function () {
    $inactivePlan = Plan::factory()->inactivo()->create();

    $this->actingAs($this->admin)
        ->postJson('/subscriptions', [
            'client_id' => $this->client->id,
            'plan_id' => $inactivePlan->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['plan_id']);

    $this->assertDatabaseMissing('subscriptions', [
        'client_id' => $this->client->id,
        'plan_id' => $inactivePlan->id,
    ]);
});

test('admin puede renovar una suscripcion', function () {
    $subscription = Subscription::factory()->expired()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    $this->actingAs($this->admin)
        ->patchJson("/subscriptions/{$subscription->id}/renew")
        ->assertOk()
        ->assertJsonPath('message', 'Suscripción renovada correctamente.')
        ->assertJsonPath('data.status', SubscriptionStatus::Active->value);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'status' => SubscriptionStatus::Active->value,
    ]);
});

test('admin puede cancelar una suscripcion', function () {
    $subscription = Subscription::factory()->active()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    $this->actingAs($this->admin)
        ->patchJson("/subscriptions/{$subscription->id}/cancel")
        ->assertOk()
        ->assertJsonPath('message', 'Suscripción cancelada correctamente.')
        ->assertJsonPath('data.status', SubscriptionStatus::Cancelled->value);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'status' => SubscriptionStatus::Cancelled->value,
    ]);
});

test('usuario sin permiso no puede registrar suscripciones', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/subscriptions', [
            'client_id' => $this->client->id,
            'plan_id' => $this->plan->id,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('subscriptions', [
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);
});
