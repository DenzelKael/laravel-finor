<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'void_reason',
        'voided_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
        'voided_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Bs ' . number_format(
            (float) $this->amount,
            2,
            ',',
            '.'
        );
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
    public function scopeForSubscription(
    Builder $query,
    int $subscriptionId
): Builder {
    return $query->where('subscription_id', $subscriptionId);
}

public function scopeForClient(
    Builder $query,
    int $clientId
): Builder {
    return $query->whereHas(
        'subscription',
        fn (Builder $subscriptionQuery) =>
            $subscriptionQuery->where('client_id', $clientId)
    );
}
/**
 * Filter registered payments from the current month.
 */
public function scopeTotalOfMonth(Builder $query): Builder
{
    return $query
        ->where(
            'status',
            PaymentStatus::Registered->value
        )
        ->whereBetween('payment_date', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ]);
}
}