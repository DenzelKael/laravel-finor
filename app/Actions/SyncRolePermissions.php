<?php

namespace App\Actions;

use App\Support\AuditLogger;
use Spatie\Permission\Models\Role;

class SyncRolePermissions
{
    public function handle(Role $role, array $permissions): void
    {
        $old = $role->permissions->pluck('name')->sort()->values()->all();

        $role->syncPermissions($permissions);

        $new = collect($permissions)->sort()->values()->all();

        if ($old !== $new) {
            AuditLogger::log('role.permissions_synced', $role, [
                'added' => array_values(array_diff($new, $old)),
                'removed' => array_values(array_diff($old, $new)),
            ]);
        }
    }
}