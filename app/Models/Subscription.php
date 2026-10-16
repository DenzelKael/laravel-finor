<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plan_id',
        'start_date',
        'expiration_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiration_date' => 'date',
        'status' => SubscriptionStatus::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Return only valid registered payments.
     */
    public function registeredPayments(): HasMany
    {
        return $this->payments()
            ->where('status', PaymentStatus::Registered->value);
    }

    /**
     * Total amount already paid for this subscription.
     */
    public function paidAmount(): float
    {
        return round(
            (float) $this->registeredPayments()->sum('amount'),
            2
        );
    }

    /**
     * Remaining balance of this subscription.
     */
    public function pendingBalance(): float
    {
        $planPrice = (float) $this->plan->precio;
        $paidAmount = $this->paidAmount();

        return max(
            0,
            round($planPrice - $paidAmount, 2)
        );
    }

    /**
     * Determine whether the subscription has been fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->pendingBalance() <= 0;
    }

public function scopeActive($query)
{
    return $query
        ->whereIn('status', [
            SubscriptionStatus::Active->value,
            SubscriptionStatus::Paid->value,
        ])
        ->where('expiration_date', '>=', today());
}
public function scopePayable($query)
{
    return $query
        ->where('status', SubscriptionStatus::Active->value)
        ->where('expiration_date', '>=', today());
}

    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatus::Expired
            || $this->expiration_date->endOfDay()->isPast();
    }
    public function syncStatusFromPayments(): void
{
    if (
        $this->status === SubscriptionStatus::Cancelled
        || $this->isExpired()
    ) {
        return;
    }

    $newStatus = $this->isFullyPaid()
        ? SubscriptionStatus::Paid
        : SubscriptionStatus::Active;

    if ($this->status !== $newStatus) {
        $this->update([
            'status' => $newStatus,
        ]);
    }
}
}