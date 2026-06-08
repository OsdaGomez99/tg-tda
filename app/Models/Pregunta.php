<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
        'tipo_tda',
        'descripcion',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($pregunta) {
            $pregunta->updateQuietly([
                'codigo' => 'P-' . str_pad($pregunta->id, 4, '0', STR_PAD_LEFT)
            ]);
        });
    }

    /**
     * Relación con respuestas
     */
    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaEncuesta::class);
    }

    /**
     * Scope para obtener preguntas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para obtener preguntas por tipo TDA (I o H)
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_tda', $tipo);
    }
}
