<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionsAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Admin->value);

        return $user;
    }

    private function vendedor(): User
    {
        $user = User::factory()->create();
        $user->assignRole('Vendedor');

        return $user;
    }

    public function test_vendedor_cannot_access_roles_index(): void
    {
        $this->actingAs($this->vendedor())
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_roles_index(): void
    {
        $this->actingAs($this->admin())
            ->get(route('roles.index'))
            ->assertOk();
    }

    public function test_admin_bypasses_permission_not_assigned_to_role(): void
    {
        // Permiso que existe pero NO está en el seeder, así que ningún rol lo tiene
        Permission::create(['name' => 'reports.view']);

        Route::middleware(['web', 'auth', 'can:reports.view'])
            ->get('/_test/gate-can', fn() => 'ok');

        Route::middleware(['web', 'auth', 'permission:reports.view'])
            ->get('/_test/gate-permission', fn() => 'ok');

        $admin = $this->admin();

        $this->assertFalse($admin->getAllPermissions()->contains('name', 'reports.view'));

        $this->actingAs($admin)->get('/_test/gate-can')->assertOk();
        $this->actingAs($admin)->get('/_test/gate-permission')->assertOk();
    }

    public function test_vendedor_is_denied_permission_not_assigned_to_role(): void
    {
        Permission::create(['name' => 'reports.view']);

        Route::middleware(['web', 'auth', 'can:reports.view'])
            ->get('/_test/gate-can', fn() => 'ok');

        $this->actingAs($this->vendedor())
            ->get('/_test/gate-can')
            ->assertForbidden();
    }

    public function test_user_role_can_be_changed_via_http(): void
    {
        $user = $this->vendedor();

        $this->actingAs($this->admin())
            ->put(route('users.update', $user), [
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
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('users.edit', $admin))
            ->put(route('users.update', $admin), [
                'role' => 'Vendedor',
                'permissions' => [],
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->hasRole(RoleName::Admin->value));
    }


    public function test_admin_can_delete_unassigned_permission_via_json(): void
    {
        $permission = Permission::create(['name' => 'tmp.view']);

        $this->actingAs($this->admin())
            ->deleteJson(route('permissions.destroy', $permission))
            ->assertOk()
            ->assertJson(['message' => 'Permiso eliminado correctamente.']);

        $this->assertModelMissing($permission);
    }

    public function test_cannot_delete_permission_assigned_to_a_role(): void
    {
        $permission = Permission::findByName('users.view');

        $this->actingAs($this->admin())
            ->deleteJson(route('permissions.destroy', $permission))
            ->assertForbidden();

        $this->assertModelExists($permission);
    }


    public function test_update_user_role_request_authorize(): void
    {
        $this->assertTrue($this->requestFor(UpdateUserRoleRequest::class, $this->admin())->authorize());
        $this->assertFalse($this->requestFor(UpdateUserRoleRequest::class, $this->vendedor())->authorize());
    }

    public function test_update_role_permissions_request_authorize(): void
    {
        $this->assertTrue($this->requestFor(UpdateRolePermissionsRequest::class, $this->admin())->authorize());
        $this->assertFalse($this->requestFor(UpdateRolePermissionsRequest::class, $this->vendedor())->authorize());
    }

    public function test_permission_request_authorize_on_store(): void
    {
        $this->assertTrue($this->requestFor(PermissionRequest::class, $this->admin())->authorize());
        $this->assertFalse($this->requestFor(PermissionRequest::class, $this->vendedor())->authorize());
    }

    private function requestFor(string $class, User $user)
    {
        $request = new $class();
        $request->setUserResolver(fn() => $user);

        return $request;
    }
}