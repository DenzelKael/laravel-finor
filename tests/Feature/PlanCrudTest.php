<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function authUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_lista_planes(): void
    {
        $this->authUser();
        Plan::factory()->count(3)->create();

        $response = $this->get(route('plans.index'));

        $response->assertOk();
        $response->assertViewIs('plans.index');
    }

    public function test_muestra_formulario_de_creacion(): void
    {
        $this->authUser();

        $response = $this->get(route('plans.create'));

        $response->assertOk();
        $response->assertViewIs('plans.create');
    }

    public function test_crea_un_plan(): void
    {
        $this->authUser();

        $payload = [
            'nombre' => 'Plan de Prueba',
            'descripcion' => 'Descripción de prueba',
            'precio' => 49.90,
            'duracion_dias' => 30,
            'activo' => 1,
        ];

        $response = $this->postJson(route('plans.store'), $payload);

        $response->assertCreated();
        $response->assertJsonStructure(['message', 'data' => ['id', 'nombre', 'precio']]);
        $this->assertDatabaseHas('plans', ['nombre' => 'Plan de Prueba']);
    }

    public function test_no_crea_plan_sin_nombre(): void
    {
        $this->authUser();

        $response = $this->postJson(route('plans.store'), [
            'precio' => 10,
            'duracion_dias' => 30,
            'activo' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nombre');
    }

    public function test_no_crea_plan_con_nombre_duplicado(): void
    {
        $this->authUser();
        Plan::factory()->create(['nombre' => 'Plan Repetido']);

        $response = $this->postJson(route('plans.store'), [
            'nombre' => 'Plan Repetido',
            'precio' => 10,
            'duracion_dias' => 30,
            'activo' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nombre');
    }

    public function test_muestra_formulario_de_edicion(): void
    {
        $this->authUser();
        $plan = Plan::factory()->create();

        $response = $this->get(route('plans.edit', $plan));

        $response->assertOk();
        $response->assertViewIs('plans.edit');
    }

    public function test_actualiza_un_plan(): void
    {
        $this->authUser();
        $plan = Plan::factory()->create(['nombre' => 'Original']);

        $response = $this->putJson(route('plans.update', $plan), [
            'nombre' => 'Actualizado',
            'precio' => 99.90,
            'duracion_dias' => 60,
            'activo' => 0,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'nombre' => 'Actualizado',
            'activo' => 0,
        ]);
    }

    public function test_elimina_un_plan(): void
    {
        $this->authUser();
        $plan = Plan::factory()->create();

        $response = $this->deleteJson(route('plans.destroy', $plan));

        $response->assertOk();
        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_requiere_autenticacion_para_listar(): void
    {
        $response = $this->get(route('plans.index'));

        $response->assertRedirect(route('login'));
    }
}
