<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('settings.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view(
            'settings.users.edit',
            compact('user', 'roles')
        );
    }
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);
        $isLastAdmin =
            $user->hasRole('Admin')
            && User::role('Admin')->count() === 1;

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
        return redirect()
            ->route('users.edit', $user)
            ->with('success', 'Role updated successfully.');
    }
}
