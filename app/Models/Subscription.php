<?php

namespace App\Models;

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

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::Active->value)
            ->where('expiration_date', '>=', today());
    }

    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatus::Expired
            || $this->expiration_date->endOfDay()->isPast();
    }
}