<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(10);
        $roles = Role::all();

        return view(
            'settings.permissions.index',
            compact('permissions', 'roles')
        );
    }

    public function create()
    {
        return view('settings.permissions.create');
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create(
            $request->validated()
        );

        $permission = Permission::create($request->validated());

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'message' => 'Permiso creado correctamente.',
            'data' => $permission,
        ]);
    }

    public function edit(Permission $permission)
    {
        return view(
            'settings.permissions.edit',
            compact('permission')
        );
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update(
            $request->validated()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'message' => 'Permiso actualizado correctamente.',
        ]);
    }

    public function destroy(Permission $permission)
    {
        abort_if(
            $permission->roles()->exists(),
            403,
            'El permiso está asignado a uno o más roles.'
        );

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'message' => 'Permiso eliminado correctamente.',
        ]);
    }
}
