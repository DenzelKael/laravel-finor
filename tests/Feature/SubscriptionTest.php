<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Client $client;

    private Plan $activePlan;

    private Plan $inactivePlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Asignar permisos requeridos por el middleware
        Permission::firstOrCreate(['name' => 'subscriptions.view']);
        Permission::firstOrCreate(['name' => 'subscriptions.create']);
        Permission::firstOrCreate(['name' => 'subscriptions.update']);
        Permission::firstOrCreate(['name' => 'subscriptions.delete']);

        $this->user->givePermissionTo([
            'subscriptions.view',
            'subscriptions.create',
            'subscriptions.update',
            'subscriptions.delete',
        ]);

        $this->client = Client::create([
            'name' => 'Cliente Test',
            'email' => 'clientetest@example.com',
            'phone' => '71112233',
            'address' => 'Av. Principal #100',
        ]);

        $this->activePlan = Plan::create([
            'nombre' => 'Plan Activo Test',
            'descripcion' => 'Descripción plan activo',
            'precio' => 100.00,
            'duracion_dias' => 30,
            'activo' => true,
        ]);

        $this->inactivePlan = Plan::create([
            'nombre' => 'Plan Inactivo Test',
            'descripcion' => 'Descripción plan inactivo',
            'precio' => 100.00,
            'duracion_dias' => 30,
            'activo' => false,
        ]);
    }

    /**
     * Test 1: Creación exitosa de suscripción con plan activo y cálculo de expiration_date.
     */
    public function test_can_create_subscription_with_active_plan(): void
    {
        $startDate = today();
        $expectedExpiration = $startDate->copy()->addDays($this->activePlan->duracion_dias);

        $response = $this->actingAs($this->user)
            ->postJson('/subscriptions', [
                'client_id' => $this->client->id,
                'plan_id' => $this->activePlan->id,
                'start_date' => $startDate->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', SubscriptionStatus::Active->value)
            ->assertJsonPath('data.expiration_date', $expectedExpiration->toDateString());

        $this->assertDatabaseHas('subscriptions', [
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'status' => SubscriptionStatus::Active->value,
        ]);

        $createdSubscription = Subscription::where('client_id', $this->client->id)->first();
        $this->assertEquals($expectedExpiration->toDateString(), $createdSubscription->expiration_date->format('Y-m-d'));
    }

    /**
     * Test 2: Rechaza creación de suscripción si el plan no está activo.
     */
    public function test_cannot_create_subscription_with_inactive_plan(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/subscriptions', [
                'client_id' => $this->client->id,
                'plan_id' => $this->inactivePlan->id,
                'start_date' => today()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plan_id']);
    }

    /**
     * Test 3: Renovación de suscripción activa o vencida actualiza expiration_date.
     */
    public function test_can_renew_active_or_expired_subscription(): void
    {
        $subscription = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->subDays(10)->toDateString(),
            'expiration_date' => today()->addDays(20)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        $expectedNewExpiration = Carbon::parse($subscription->expiration_date)
            ->copy()
            ->addDays($this->activePlan->duracion_dias)
            ->toDateString();

        $response = $this->actingAs($this->user)
            ->patchJson("/subscriptions/{$subscription->id}/renew");

        $response->assertStatus(200)
            ->assertJsonPath('data.expiration_date', $expectedNewExpiration)
            ->assertJsonPath('data.status', SubscriptionStatus::Active->value);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => SubscriptionStatus::Active->value,
        ]);

        $this->assertEquals($expectedNewExpiration, $subscription->fresh()->expiration_date->format('Y-m-d'));
    }

    /**
     * Test 4: Rechaza renovar una suscripción que está CANCELLED (error 422).
     */
    public function test_cannot_renew_cancelled_subscription(): void
    {
        $subscription = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->subDays(10)->toDateString(),
            'expiration_date' => today()->addDays(20)->toDateString(),
            'status' => SubscriptionStatus::Cancelled,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/subscriptions/{$subscription->id}/renew");

        $response->assertStatus(422);
    }

    /**
     * Test 5: Cancela una suscripción cambiando su estado a CANCELLED.
     */
    public function test_can_cancel_subscription(): void
    {
        $subscription = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/subscriptions/{$subscription->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', SubscriptionStatus::Cancelled->value);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => SubscriptionStatus::Cancelled->value,
        ]);
    }

    /**
     * Test 6: Rechaza cancelar una suscripción que ya está cancelada (error 422).
     */
    public function test_cannot_cancel_already_cancelled_subscription(): void
    {
        $subscription = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Cancelled,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/subscriptions/{$subscription->id}/cancel");

        $response->assertStatus(422);
    }

    /**
     * Test 7: Un usuario sin los permisos correspondientes recibe error 403 Forbidden.
     */
    public function test_user_without_permissions_receives_403(): void
    {
        $unauthorizedUser = User::factory()->create();

        // Intento de listar suscripciones sin permiso subscriptions.view
        $response = $this->actingAs($unauthorizedUser)
            ->getJson('/subscriptions/data');

        $response->assertStatus(403);

        // Intento de crear suscripción sin permiso subscriptions.create
        $createResponse = $this->actingAs($unauthorizedUser)
            ->postJson('/subscriptions', [
                'client_id' => $this->client->id,
                'plan_id' => $this->activePlan->id,
            ]);

        $createResponse->assertStatus(403);
    }

    /**
     * Test 8: Un usuario autorizado puede obtener el listado JSON desde /subscriptions/data.
     */
    public function test_authorized_user_can_retrieve_subscriptions_data_json(): void
    {
        Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/subscriptions/data');

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'client_id',
                    'plan_id',
                    'start_date',
                    'expiration_date',
                    'status',
                    'client',
                    'plan',
                ],
            ]);
    }

    /**
     * Test 9: No se puede duplicar una suscripción activa para un mismo cliente.
     */
    public function test_cannot_create_duplicate_active_subscription_for_same_client(): void
    {
        // Ya existe una suscripción activa
        Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        // Intentar crear otra suscripción activa para el mismo cliente
        $response = $this->actingAs($this->user)
            ->postJson('/subscriptions', [
                'client_id' => $this->client->id,
                'plan_id' => $this->activePlan->id,
                'start_date' => today()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_id']);
    }

    /**
     * Test 10: El comando subscriptions:expire marca como vencidas las suscripciones pasadas.
     */
    public function test_expire_subscriptions_command_marks_past_subscriptions_as_expired(): void
    {
        // Suscripción con fecha vencida pero status ACTIVE
        $expiredSub = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->subDays(40)->toDateString(),
            'expiration_date' => today()->subDays(5)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        // Suscripción vigente que debe seguir ACTIVE
        $activeSub = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(20)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        $this->artisan('subscriptions:expire')
            ->assertExitCode(0);

        $this->assertEquals(SubscriptionStatus::Expired, $expiredSub->fresh()->status);
        $this->assertEquals(SubscriptionStatus::Active, $activeSub->fresh()->status);
    }

    /**
     * Test 11: Client y Plan tienen relación hasMany con Subscription.
     */
    public function test_client_and_plan_have_inverse_has_many_subscriptions_relation(): void
    {
        $sub = Subscription::create([
            'client_id' => $this->client->id,
            'plan_id' => $this->activePlan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        $this->assertTrue($this->client->subscriptions->contains($sub));
        $this->assertTrue($this->activePlan->subscriptions->contains($sub));
    }
}
