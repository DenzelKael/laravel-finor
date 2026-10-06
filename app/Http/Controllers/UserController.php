<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Enums\RoleName;


class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('settings.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view(
            'settings.users.edit',
            compact(
                'user',
                'roles',
                'permissions'
            )
        );
    }
    public function update(UpdateUserRoleRequest $request, User $user)
    {
        $validated = $request->validated();
        $isLastAdmin =
            $user->hasRole(RoleName::Admin->value)
            && User::role(RoleName::Admin->value)->count() === 1;

        if (
            $isLastAdmin
            && $validated['role'] !== 'Admin'
        ) {
            return back()
                ->withErrors([
                    'role' => 'Debe existir al menos un administrador.'
                ]);
        }

        $user->syncRoles($validated['role']);
        $user->syncPermissions(
            $validated['permissions'] ?? []
        );
        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
        ]);
    }
}
