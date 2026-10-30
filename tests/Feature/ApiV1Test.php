<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    public function test_ping_usa_prefijo_v1(): void
    {
        $this->getJson('/api/v1/ping')->assertOk()->assertJson(['status' => 'ok']);
    }

    public function test_lista_planes_con_resource(): void
    {
        Plan::factory()->count(2)->create(['activo' => true]);

        $this->getJson('/api/v1/plans')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'nombre', 'precio', 'duracion_dias', 'activo']]]);
    }
}
