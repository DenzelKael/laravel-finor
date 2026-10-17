<?php

namespace App\Models;

use App\Enums\ClientStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    // Tras create() sin status, el modelo ya tiene Active (lo necesita ClientResource).
    protected $attributes = [
        'status' => ClientStatus::Active->value,
    ];

    protected function casts(): array
    {
        return ['status' => ClientStatus::class];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ClientStatus::Active);
    }

    public function scopeOfStatus(Builder $query, ?ClientStatus $status): Builder
    {
        return $query->when($status, fn (Builder $q) => $q->where('status', $status));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        // El where(function) agrupa el OR: (name LIKE ... OR email LIKE ...) AND status = ...
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        }));
    }
}