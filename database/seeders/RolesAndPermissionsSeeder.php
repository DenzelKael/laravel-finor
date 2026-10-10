<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Roles
        $admin = Role::firstOrCreate([
            'name' => 'Admin',
        ]);

        $vendedor = Role::firstOrCreate([
            'name' => 'Vendedor',
        ]);
        // Users
        Permission::firstOrCreate(['name' => 'users.view']);
        Permission::firstOrCreate(['name' => 'users.create']);
        Permission::firstOrCreate(['name' => 'users.update']);
        Permission::firstOrCreate(['name' => 'users.delete']);

        // Roles
        Permission::firstOrCreate(['name' => 'roles.view']);
        Permission::firstOrCreate(['name' => 'roles.create']);
        Permission::firstOrCreate(['name' => 'roles.update']);
        Permission::firstOrCreate(['name' => 'roles.delete']);

        // Permissions
        Permission::firstOrCreate(['name' => 'permissions.view']);
        Permission::firstOrCreate(['name' => 'permissions.create']);
        Permission::firstOrCreate(['name' => 'permissions.update']);
        Permission::firstOrCreate(['name' => 'permissions.delete']);

        // Subscriptions
        Permission::firstOrCreate(['name' => 'subscriptions.view']);
        Permission::firstOrCreate(['name' => 'subscriptions.create']);
        Permission::firstOrCreate(['name' => 'subscriptions.renew']);
        Permission::firstOrCreate(['name' => 'subscriptions.cancel']);

        $admin->syncPermissions([
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',

            'subscriptions.view',
            'subscriptions.create',
            'subscriptions.renew',
            'subscriptions.cancel',

        ]);

        $vendedor->syncPermissions([
            'users.view',
        ]);
    }
}
