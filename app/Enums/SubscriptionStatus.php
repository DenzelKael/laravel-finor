<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'ACTIVE';
    case Paid = 'PAID';
    case Expired = 'EXPIRED';
    case Cancelled = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Paid => 'Pagada',
            self::Expired => 'Vencida',
            self::Cancelled => 'Cancelada',
        };
    }
}