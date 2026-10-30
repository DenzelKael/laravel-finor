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
        Permission::findOrCreate('payments.view', 'web');
        $user = User::factory()->create();
        $user->givePermissionTo('payments.view');

        $this->actingAs($user)
            ->getJson(route('api.chart-data.index'))
            ->assertOk()
            ->assertExactJson([
                'labels' => ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                'data' => [150, 300, 250, 400],
            ]);
    }

    public function test_chart_data_sin_permiso_recibe_403(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('api.chart-data.index'))
            ->assertForbidden();
    }

    public function test_chart_data_sin_login_es_rechazado(): void
    {
        $this->getJson(route('api.chart-data.index'))->assertUnauthorized();
    }
}
