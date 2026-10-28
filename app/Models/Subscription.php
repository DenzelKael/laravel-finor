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
     * Retorna únicamente los pagos registrados válidos.
     */
    public function registeredPayments(): HasMany
    {
        return $this->payments()
            ->where(
                'status',
                PaymentStatus::Registered->value
            );
    }

    /**
     * Retorna el total pagado de la suscripción.
     */
    public function paidAmount(): float
    {
        return round(
            (float) $this->registeredPayments()->sum('amount'),
            2
        );
    }

    /**
     * Retorna el saldo pendiente de la suscripción.
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
     * Determina si la suscripción está completamente pagada.
     *
     * El estado financiero se calcula a partir de los pagos
     * y no modifica el ciclo de vida de la suscripción.
     */
    public function isFullyPaid(): bool
    {
        return $this->pendingBalance() <= 0;
    }

    /**
     * Suscripciones activas según su ciclo de vida.
     */
    public function scopeActive($query)
    {
        return $query
            ->where(
                'status',
                SubscriptionStatus::Active->value
            )
            ->where(
                'expiration_date',
                '>=',
                today()
            );
    }

    /**
     * Determina si la suscripción está vencida.
     */
    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatus::Expired
            || $this->expiration_date
                ->endOfDay()
                ->isPast();
    }
}
