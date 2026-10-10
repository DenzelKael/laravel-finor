<?php

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(SubscriptionService::class);

    $this->client = Client::create([
        'name' => 'Cliente de prueba',
        'email' => 'cliente@example.com',
        'phone' => null,
        'address' => null,
    ]);

    $this->plan = Plan::factory()->create([
        'duracion_dias' => 30,
        'activo' => true,
    ]);
});

test('crea una suscripcion con fecha de vencimiento calculada', function () {
    $subscription = $this->service->create($this->client, $this->plan);

    expect($subscription->start_date->toDateString())
        ->toBe(today()->toDateString())
        ->and($subscription->expiration_date->toDateString())
        ->toBe(today()->addDays(30)->toDateString())
        ->and($subscription->status)
        ->toBe(SubscriptionStatus::Active);
});

test('renueva una suscripcion y actualiza su vencimiento', function () {
    $subscription = Subscription::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'start_date' => today()->subDays(10),
        'expiration_date' => today()->subDays(1),
        'status' => SubscriptionStatus::Expired,
    ]);

    $renewed = $this->service->renew($subscription);

    expect($renewed->start_date->toDateString())
        ->toBe(today()->toDateString())
        ->and($renewed->expiration_date->toDateString())
        ->toBe(today()->addDays(30)->toDateString())
        ->and($renewed->status)
        ->toBe(SubscriptionStatus::Active);
});

test('cancela una suscripcion activa', function () {
    $subscription = Subscription::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => SubscriptionStatus::Active,
    ]);

    $cancelled = $this->service->cancel($subscription);

    expect($cancelled->status)
        ->toBe(SubscriptionStatus::Cancelled);
});

test('no permite renovar una suscripcion cancelada', function () {
    $subscription = Subscription::factory()->cancelled()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    expect(fn () => $this->service->renew($subscription))
        ->toThrow(ValidationException::class);
});
