<?php

namespace App\Observers;

use App\Support\AuditLogger;
use Spatie\Permission\Models\Permission;

class PermissionObserver
{
    public function created(Permission $permission): void
    {
        AuditLogger::log('permission.created', $permission, ['name' => $permission->name]);
    }

    public function updated(Permission $permission): void
    {
        if (! $permission->wasChanged('name')) {
            return;
        }

        AuditLogger::log('permission.updated', $permission, [
            'old' => $permission->getOriginal('name'),
            'new' => $permission->name,
        ]);
    }

    public function deleted(Permission $permission): void
    {
        AuditLogger::log('permission.deleted', $permission, ['name' => $permission->name]);
    }
}
