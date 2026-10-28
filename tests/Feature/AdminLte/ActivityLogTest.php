<?php

namespace Tests\Feature\AdminLte;

use App\Enums\RoleName;
use App\Models\User;
use ColorlibHQ\AdminLte\Support\ActivityLogger;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ActivityLogger::flushTableCache();
        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guests_are_redirected_from_the_activity_log(): void
    {
        $this->get(route('adminlte.activity.index'))
            ->assertRedirect(route('login'));
    }

    public function test_an_admin_can_view_the_activity_log(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Admin->value);

        $this->actingAs($admin)
            ->get(route('adminlte.activity.index'))
            ->assertOk();
    }

    public function test_a_user_with_audit_view_permission_can_view_the_activity_log(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('audit.view');

        $this->actingAs($user)
            ->get(route('adminlte.activity.index'))
            ->assertOk();
    }

    public function test_a_user_without_audit_permission_cannot_view_the_activity_log(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('adminlte.activity.index'))
            ->assertForbidden();
    }

    public function test_the_logger_records_an_activity_row(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        ActivityLogger::log('test.event', 'Something happened', ['foo' => 'bar']);

        $this->assertDatabaseHas('activity_log', [
            'user_id' => $user->id,
            'event' => 'test.event',
            'description' => 'Something happened',
        ]);
    }
}