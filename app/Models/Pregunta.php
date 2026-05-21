<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'estado',
        'tipo_tda',
        'ejemplo',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
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
