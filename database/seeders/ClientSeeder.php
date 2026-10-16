<?php

namespace Database\Seeders;

use App\Enums\ClientStatus;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::factory()->count(30)->create();
        Client::factory()->count(10)->create(['status' => ClientStatus::Inactive]);
    }
}