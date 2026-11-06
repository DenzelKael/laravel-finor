<?php

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::firstOrCreate(
            ['email' => 'juan.perez@example.com'],
            [
                'name' => 'Juan Pérez',
                'phone' => '71234567',
                'address' => 'Av. San Martín #123',
            ]
        );

        $client2 = Client::firstOrCreate(
            ['email' => 'maria.lopez@example.com'],
            [
                'name' => 'María López',
                'phone' => '79876543',
                'address' => 'Calle Bolívar #456',
            ]
        );

        $client3 = Client::firstOrCreate(
            ['email' => 'carlos.mendoza@example.com'],
            [
                'name' => 'Carlos Mendoza',
                'phone' => '70123456',
                'address' => 'Av. Cristo Redentor #789',
            ]
        );

        $planBasic = Plan::firstOrCreate(
            ['nombre' => 'Plan Básico'],
            [
                'descripcion' => 'Acceso esencial al servicio',
                'precio' => 100.00,
                'duracion_dias' => 30,
                'activo' => true,
            ]
        );

        $planPremium = Plan::firstOrCreate(
            ['nombre' => 'Plan Premium'],
            [
                'descripcion' => 'Acceso total y soporte prioritario',
                'precio' => 250.00,
                'duracion_dias' => 30,
                'activo' => true,
            ]
        );

        $planStandard = Plan::firstOrCreate(
            ['nombre' => 'Plan Estándar'],
            [
                'descripcion' => 'Acceso estándar recomendado',
                'precio' => 150.00,
                'duracion_dias' => 30,
                'activo' => true,
            ]
        );

        // Suscripción activa (Juan Pérez - Plan Básico)
        Subscription::firstOrCreate(
            [
                'client_id' => $client1->id,
                'plan_id' => $planBasic->id,
            ],
            [
                'start_date' => now()->startOfMonth()->toDateString(),
                'expiration_date' => now()->addMonths(3)->endOfDay()->toDateString(),
                'status' => SubscriptionStatus::Active->value,
            ]
        );

        // Suscripción vencida (María López - Plan Premium)
        Subscription::firstOrCreate(
            [
                'client_id' => $client2->id,
                'plan_id' => $planPremium->id,
            ],
            [
                'start_date' => now()->subMonths(4)->startOfMonth()->toDateString(),
                'expiration_date' => now()->subDays(5)->endOfDay()->toDateString(),
                'status' => SubscriptionStatus::Expired->value,
            ]
        );

        // Suscripción activa (Carlos Mendoza - Plan Estándar)
        Subscription::firstOrCreate(
            [
                'client_id' => $client3->id,
                'plan_id' => $planStandard->id,
            ],
            [
                'start_date' => now()->subDays(10)->toDateString(),
                'expiration_date' => now()->addMonths(6)->endOfDay()->toDateString(),
                'status' => SubscriptionStatus::Active->value,
            ]
        );

        // Generar clientes adicionales si hay menos de 150
        $existingClientsCount = Client::count();
        if ($existingClientsCount < 150) {
            Client::factory()->count(150 - $existingClientsCount)->create();
        }

        $clientIds = Client::pluck('id')->all();
        $planIds = Plan::pluck('id')->all();

        if (empty($clientIds) || empty($planIds)) {
            return;
        }

        $now = now();
        $subscriptions = [];

        // 1. 150 suscripciones por vencer en los próximos 1 a 7 días
        for ($i = 0; $i < 150; $i++) {
            $days = rand(1, 7);
            $subscriptions[] = [
                'client_id' => $clientIds[array_rand($clientIds)],
                'plan_id' => $planIds[array_rand($planIds)],
                'start_date' => today()->subDays(30 - $days)->toDateString(),
                'expiration_date' => today()->addDays($days)->toDateString(),
                'status' => SubscriptionStatus::Active->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 2. 150 suscripciones por vencer en los próximos 8 a 15 días
        for ($i = 0; $i < 150; $i++) {
            $days = rand(8, 15);
            $subscriptions[] = [
                'client_id' => $clientIds[array_rand($clientIds)],
                'plan_id' => $planIds[array_rand($planIds)],
                'start_date' => today()->subDays(30 - $days)->toDateString(),
                'expiration_date' => today()->addDays($days)->toDateString(),
                'status' => SubscriptionStatus::Active->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 3. 200 suscripciones por vencer en los próximos 16 a 30 días
        for ($i = 0; $i < 200; $i++) {
            $days = rand(16, 30);
            $subscriptions[] = [
                'client_id' => $clientIds[array_rand($clientIds)],
                'plan_id' => $planIds[array_rand($planIds)],
                'start_date' => today()->subDays(30 - $days)->toDateString(),
                'expiration_date' => today()->addDays($days)->toDateString(),
                'status' => SubscriptionStatus::Active->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 4. 100 suscripciones a largo plazo (> 30 a 120 días)
        for ($i = 0; $i < 100; $i++) {
            $days = rand(31, 120);
            $subscriptions[] = [
                'client_id' => $clientIds[array_rand($clientIds)],
                'plan_id' => $planIds[array_rand($planIds)],
                'start_date' => today()->subDays(10)->toDateString(),
                'expiration_date' => today()->addDays($days)->toDateString(),
                'status' => SubscriptionStatus::Active->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 5. 50 suscripciones vencidas en el pasado (1 a 45 días atrás)
        for ($i = 0; $i < 50; $i++) {
            $daysAgo = rand(1, 45);
            $subscriptions[] = [
                'client_id' => $clientIds[array_rand($clientIds)],
                'plan_id' => $planIds[array_rand($planIds)],
                'start_date' => today()->subDays(30 + $daysAgo)->toDateString(),
                'expiration_date' => today()->subDays($daysAgo)->toDateString(),
                'status' => SubscriptionStatus::Expired->value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Inserción en lotes de 200 para máxima velocidad
        foreach (array_chunk($subscriptions, 200) as $chunk) {
            Subscription::insert($chunk);
        }
    }
}
