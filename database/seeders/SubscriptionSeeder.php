<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::updateOrCreate(
            ['id' => 1],
            [
                'customer_name' => 'Juan Pérez',
                'plan_name' => 'Plan Básico',
                'start_date' => '2026-09-01',
                'expiration_date' => '2026-12-31',
                'status' => 'ACTIVE',
            ]
        );

        Subscription::updateOrCreate(
            ['id' => 2],
            [
                'customer_name' => 'María López',
                'plan_name' => 'Plan Premium',
                'start_date' => '2026-01-01',
                'expiration_date' => '2026-08-31',
                'status' => 'EXPIRED',
            ]
        );

        Subscription::updateOrCreate(
            ['id' => 3],
            [
                'customer_name' => 'Carlos Mendoza',
                'plan_name' => 'Plan Estándar',
                'start_date' => '2026-09-10',
                'expiration_date' => '2027-03-10',
                'status' => 'ACTIVE',
            ]
        );
    }
}