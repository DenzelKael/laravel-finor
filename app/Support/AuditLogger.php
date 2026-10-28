<?php

namespace App\Support;

use ColorlibHQ\AdminLte\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(string $event, ?Model $subject = null, array $properties = [], ?string $description = null): void
    {
        ActivityLogger::log($event, $description ?? $event, $properties, $subject);
    }
}