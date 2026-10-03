<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion_dias',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio' => 'decimal:2',
    ];

    /**
     * Scope para traer solo los planes activos.
     * Uso: Plan::activo()->get();
     */
    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Precio formateado para mostrar en vistas, ej: "$29.90"
     */
    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format((float) $this->precio, 2);
    }
}
