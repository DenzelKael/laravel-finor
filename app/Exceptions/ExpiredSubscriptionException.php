<?php

namespace App\Exceptions;

use Exception;

class ExpiredSubscriptionException extends Exception
{
    public function __construct(
        string $message = 'No se puede registrar el pago porque la suscripción está vencida.'
    ) {
        parent::__construct($message);
    }
}