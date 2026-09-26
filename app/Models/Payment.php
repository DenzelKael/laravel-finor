<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'subscription_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'CASH' => 'Efectivo',
            'CARD' => 'Tarjeta',
            'TRANSFER' => 'Transferencia bancaria',
            'QR' => 'QR',
            default => $this->payment_method,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'REGISTERED' => 'Registrado',
            'CANCELLED' => 'Anulado',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'REGISTERED' => 'success',
            'CANCELLED' => 'danger',
            default => 'secondary',
        };
    }
}