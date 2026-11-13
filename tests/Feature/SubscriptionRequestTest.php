<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SubscriptionRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'subscriptions.create']);
    }

    private function requestFor(?User $user, array $data = []): SubscriptionRequest
    {
        $request = SubscriptionRequest::create('/subscriptions', 'POST', $data);
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        $request = $this->requestFor(null, $data);

        return Validator::make($data, $request->rules(), $request->messages());
    }

    private function client(): Client
    {
        return Client::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'phone' => '70000000',
            'address' => 'Av. Test',
        ]);
    }

    private function plan(bool $activo): Plan
    {
        return Plan::create([
            'nombre' => $activo ? 'Plan Activo' : 'Plan Inactivo',
            'descripcion' => 'Plan de prueba',
            'precio' => 100,
            'duracion_dias' => 30,
            'activo' => $activo,
        ]);
    }

    public function test_authorize_allows_user_with_create_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('subscriptions.create');

        $this->assertTrue($this->requestFor($user)->authorize());
    }

    public function test_authorize_denies_user_without_create_permission(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($this->requestFor($user)->authorize());
    }

    public function test_authorize_denies_guest(): void
    {
        $this->assertFalse($this->requestFor(null)->authorize());
    }

    public function test_rules_accept_active_plan(): void
    {
        $validator = $this->validate([
            'client_id' => $this->client()->id,
            'plan_id' => $this->plan(true)->id,
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_rules_reject_inactive_plan_with_clear_message(): void
    {
        $validator = $this->validate([
            'client_id' => $this->client()->id,
            'plan_id' => $this->plan(false)->id,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'El plan seleccionado no existe o no está activo.',
            $validator->errors()->first('plan_id')
        );
    }

    public function test_request_does_not_check_active_subscription_rule(): void
    {
        // La regla "una sola activa por cliente" vive solo en SubscriptionService.
        $client = $this->client();
        $plan = $this->plan(true);

        Subscription::create([
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'start_date' => today()->toDateString(),
            'expiration_date' => today()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);

        $data = ['client_id' => $client->id, 'plan_id' => $plan->id];
        $request = $this->requestFor(null, $data);

        $this->assertFalse(method_exists($request, 'withValidator'));
        $this->assertTrue($this->validate($data)->passes());
    }

    public function test_rules_reject_nonexistent_plan(): void
    {
        $validator = $this->validate([
            'client_id' => $this->client()->id,
            'plan_id' => 999,
        ]);

        $this->assertTrue($validator->errors()->has('plan_id'));
    }
}
