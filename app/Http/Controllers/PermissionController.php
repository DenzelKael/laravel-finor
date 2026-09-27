<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'regex:/^[a-z_]+\.[a-z_]+$/',
                'unique:permissions,name',
            ],
        ]);
        Permission::create([
            'name' => $validated['name'],
        ]);
        return redirect()
            ->route('permissions.index');
    }
    public function edit(Permission $permission)
    {
        return view(
            'settings.permissions.edit',
            compact('permission')
        );
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'regex:/^[a-z_]+\.[a-z_]+$/',
                'unique:permissions,name,' . $permission->id,
            ],
        ]);
        $permission->update([
            'name' => $validated['name'],
        ]);
        return redirect()
            ->route('permissions.index');
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
