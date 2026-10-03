<?php

namespace Database\Seeders;

use App\Models\Client; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Generamos 50 clients
        Client::factory()->count(50)->create();
    }
}