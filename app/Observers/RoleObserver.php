<?php

namespace App\Observers;

use App\Support\AuditLogger;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    public function created(Role $role): void
    {
        AuditLogger::log('role.created', $role, ['name' => $role->name]);
    }

    public function updated(Role $role): void
    {
        if (!$role->wasChanged('name')) {
            return;
        }

        AuditLogger::log('role.updated', $role, [
            'old' => $role->getOriginal('name'),
            'new' => $role->name,
        ]);
    }

    public function deleted(Role $role): void
    {
        AuditLogger::log('role.deleted', $role, ['name' => $role->name]);
    }
}