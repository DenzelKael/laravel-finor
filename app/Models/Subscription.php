<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'customer_name',
        'plan_name',
        'start_date',
        'expiration_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isExpired(): bool
    {
        return $this->status === 'EXPIRED'
            || $this->expiration_date->isPast();
    }
}