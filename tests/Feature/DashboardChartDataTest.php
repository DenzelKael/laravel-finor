<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardChartDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_authorized_user_can_retrieve_chart_data(): void
    {
        $user = User::factory()->create();

        $user->givePermissionTo('payments.view');

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.chart-data.index'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'labels',
                'data',
            ]);
    }

    public function test_guest_cannot_retrieve_chart_data(): void
    {
        $response = $this->getJson(
            route('api.chart-data.index')
        );

        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_retrieve_chart_data(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.chart-data.index'));

        $response->assertForbidden();
    }
}
