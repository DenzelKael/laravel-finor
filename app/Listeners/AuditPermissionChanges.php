<?php

namespace App\Listeners;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuditPermissionChanges
{
    public function handlePermissionAttached(PermissionAttachedEvent $event): void
    {
        $this->logPermissions('attached', $event->model, $event->permissionsOrIds);
    }

    public function handlePermissionDetached(PermissionDetachedEvent $event): void
    {
        $this->logPermissions('detached', $event->model, $event->permissionsOrIds);
    }

    public function handleRoleAttached(RoleAttachedEvent $event): void
    {
        $this->logRoles('attached', $event->model, $event->rolesOrIds);
    }

    public function handleRoleDetached(RoleDetachedEvent $event): void
    {
        $this->logRoles('detached', $event->model, $event->rolesOrIds);
    }

    private function logPermissions(string $action, Model $model, mixed $items): void
    {
        $names = $this->names($items, Permission::class);

        if ($names === []) {
            return;
        }

        $target = $model instanceof Role ? $model->name : ($model->name ?? $model->getKey());
        $verb = $action === 'attached' ? 'asignados' : 'quitados';

        AuditLogger::log(
            "permission.{$action}",
            $model,
            ['permissions' => $names],
            "Permisos {$verb} a «{$target}»: " . implode(', ', $names)
        );
    }

    private function logRoles(string $action, Model $model, mixed $items): void
    {
        $names = $this->names($items, Role::class);

        if ($names === []) {
            return;
        }

        $target = $model instanceof User ? $model->email : $model->getKey();
        $verb = $action === 'attached' ? 'asignados' : 'quitados';

        AuditLogger::log(
            "role.{$action}",
            $model,
            ['roles' => $names],
            "Roles {$verb} a «{$target}»: " . implode(', ', $names)
        );
    }

    /**
     * Normaliza ids, modelos, arrays o colecciones a una lista de nombres.
     */
    private function names(mixed $items, string $modelClass): array
    {
        $items = $items instanceof Collection ? $items->all() : (array) $items;

        $names = [];
        $ids = [];

        foreach ($items as $item) {
            if ($item instanceof Model) {
                $names[] = $item->name;
            } else {
                $ids[] = $item;
            }
        }

        if ($ids !== []) {
            $names = array_merge($names, $modelClass::whereIn('id', $ids)->pluck('name')->all());
        }

        sort($names);

        return array_values($names);
    }
}