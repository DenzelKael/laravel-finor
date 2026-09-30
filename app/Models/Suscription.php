<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Suscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($suscription) {
            if ($suscription->plan_id && $suscription->start_date) {
                $plan = Plan::find($suscription->plan_id);
                if ($plan && $plan->duracion_dias) {
                    $suscription->end_date = Carbon::parse($suscription->start_date)->addDays($plan->duracion_dias);
                }
            }
        });
        
        static::updating(function ($suscription) {
            if ($suscription->isDirty('plan_id') || $suscription->isDirty('start_date')) {
                $plan = Plan::find($suscription->plan_id);
                if ($plan && $plan->duracion_dias) {
                    $suscription->end_date = Carbon::parse($suscription->start_date)->addDays($plan->duracion_dias);
                }
            }
        });
    }
}
