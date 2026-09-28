<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('users', 'permissions')->get();

        return view('settings.roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('settings.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        return redirect()
            ->route('roles.edit', $role);
    }
}
