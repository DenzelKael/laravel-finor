<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Enums\RoleName;

class PermissionsAccessTest extends TestCase
{
    use RefreshDatabase;
    public function test_vendedor_cannot_access_roles_index(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('Vendedor');
        $response = $this->actingAs($user)
            ->get('/admin/roles');
        $response->assertForbidden();
    }
    public function test_user_direct_permissions_can_be_changed(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();

        $user->syncPermissions([
            'users.view',
        ]);

        $this->assertTrue(
            $user->hasDirectPermission('users.view')
        );

        $user->syncPermissions([
            'roles.view',
        ]);

        $user = $user->fresh();

        $this->assertTrue(
            $user->hasDirectPermission('roles.view')
        );

        $this->assertFalse(
            $user->hasDirectPermission('users.view')
        );
    }
    public function test_user_role_can_be_changed_via_http(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Admin->value);

        $user = User::factory()->create();
        $user->assignRole('Vendedor');

        $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", [
                'role' => 'Admin',
                'permissions' => [],
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'Usuario actualizado correctamente.',
            ]);

        $this->assertTrue($user->fresh()->hasRole('Admin'));
    }
    public function test_last_admin_cannot_be_demoted(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Admin->value);

        // Único admin intenta quitarse su propio rol
        $this->actingAs($admin)
            ->from("/admin/users/{$admin->id}/edit")
            ->put("/admin/users/{$admin->id}", [
                'role' => 'Vendedor',
                'permissions' => [],
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->hasRole(RoleName::Admin->value));
    }
    public function test_admin_can_access_roles_index(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleName::Admin->value);

        $this->actingAs($admin)->get('/admin/roles')->assertOk();
    }
}