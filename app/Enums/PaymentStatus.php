<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Registered = 'REGISTERED';
    case Cancelled = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Registrado',
            self::Cancelled => 'Anulado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Registered => 'badge-success',
            self::Cancelled => 'badge-danger',
        };
    }
}
