<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('q');
        $role = $request->input('role');

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->role($role);
            })
            ->with('roles')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('settings.users.index', compact('users', 'roles', 'search', 'role'));
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
                    'role' => 'Debe existir al menos un administrador.',
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

    public function create()
    {
        $roles = Role::all();

        return view('settings.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }
}
