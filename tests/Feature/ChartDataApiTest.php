<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ChartDataApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chart_data_devuelve_labels_y_data(): void
    {
        $user = User::factory()->create();

        $permission = Permission::findOrCreate('payments.view');
        $user->givePermissionTo($permission);

        $this->actingAs($user)
            ->getJson(route('dashboard.chart'))
            ->assertOk()
            ->assertJsonStructure([
                'labels',
                'data',
            ])
            ->assertJson([
                'labels' => [],
                'data' => [],
            ]);
    }

    public function test_chart_data_sin_permiso_recibe_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('dashboard.chart'))
            ->assertForbidden();
    }

    public function test_chart_data_sin_login_es_rechazado(): void
    {
        $this->getJson(route('dashboard.chart'))
            ->assertUnauthorized();
    }
}
