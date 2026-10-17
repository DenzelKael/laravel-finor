<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Registered = 'REGISTERED';
    case Voided = 'VOIDED';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Registrado',
            self::Voided => 'Anulado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Registered => 'badge-success',
            self::Voided => 'badge-danger',
        };
    }
}