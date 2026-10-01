<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
        return response()->json([
            'message' => 'Permiso actualizado correctamente.',
        ]);
    }
    public function destroy(Permission $permission)
    {
        abort_if(
            $permission->roles()->exists(),
            403,
            'The permission is assigned to one or more roles.'
        );
        $permission->delete();
        return redirect()
            ->route('permissions.index');
    }
}
