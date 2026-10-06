<?php

namespace App\DTOs;

use App\Enums\PaymentMethod;
use Carbon\Carbon;

final readonly class PaymentData
{
    public function __construct(
        public float $amount,
        public PaymentMethod $paymentMethod,
        public Carbon $paymentDate,
    ) {}

    /**
     * Create DTO from validated request data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            paymentMethod: $data['payment_method'] instanceof PaymentMethod
                ? $data['payment_method']
                : PaymentMethod::from($data['payment_method']),
            paymentDate: Carbon::parse($data['payment_date']),
        );
    }

    /**
     * Convert DTO to an array for persistence.
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod->value,
            'payment_date' => $this->paymentDate->toDateString(),
        ];
    }
}