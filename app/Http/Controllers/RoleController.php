<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UpdateRolePermissionsRequest;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('users', 'permissions')
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

        return response()->json([
            'message' => 'Rol actualizado correctamente.',
        ]);
    }
}
