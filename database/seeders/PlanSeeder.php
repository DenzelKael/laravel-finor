<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::factory()->count(8)->create();
        Plan::factory()->count(2)->inactivo()->create();
    }
}