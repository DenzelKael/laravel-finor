<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

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
        'status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Bs ' . number_format((float) $this->amount, 2, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method instanceof PaymentMethod
            ? $this->payment_method->label()
            : (string) $this->payment_method;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof PaymentStatus
            ? $this->status->label()
            : (string) $this->status;
    }
}