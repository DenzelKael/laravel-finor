<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'ACTIVE';
    case Expired = 'EXPIRED';
    case Cancelled = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Expired => 'Vencida',
            self::Cancelled => 'Cancelada',
        };
    }
}
