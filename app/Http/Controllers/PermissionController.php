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
}
