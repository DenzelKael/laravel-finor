<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table = 'suscriptions';

    protected $fillable = [
        'cliente_id',
        'plan_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
