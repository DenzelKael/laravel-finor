<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'CASH';
    case Card = 'CARD';
    case Transfer = 'TRANSFER';
    case Qr = 'QR';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Efectivo',
            self::Card => 'Tarjeta',
            self::Transfer => 'Transferencia bancaria',
            self::Qr => 'QR',
        };
    }
}
