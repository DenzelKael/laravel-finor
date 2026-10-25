<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Enums\RoleName;
use App\Http\Requests\StoreRoleRequest;

use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->with('permissions')
            ->paginate(10);

        return view('settings.roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('settings.roles.edit', compact('role', 'permissions'));
    }


    public function update(UpdateRolePermissionsRequest $request, Role $role)
    {
        $validated = $request->validated();

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'message' => 'Rol actualizado correctamente.',
        ]);
    }

    public function destroy(Role $role)
    {
        abort_if(
            $role->name === RoleName::Admin->value,
            403,
            'El rol Admin no se puede eliminar.'
        );

        abort_if(
            $role->users()->exists(),
            403,
            'El rol tiene usuarios asignados.'
        );

        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado correctamente.',
        ]);
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('settings.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'message' => 'Rol creado correctamente.',
            'data' => $role,
        ]);
    }

}
