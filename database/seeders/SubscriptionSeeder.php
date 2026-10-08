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
                'end_date' => now()->addMonths(3)->endOfDay()->toDateString(),
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
                'end_date' => now()->subDays(5)->endOfDay()->toDateString(),
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
                'end_date' => now()->addMonths(6)->endOfDay()->toDateString(),
                'status' => SubscriptionStatus::Active->value,
            ]
        );
    }
}