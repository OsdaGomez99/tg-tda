<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncuestaResultado extends Model
{
    protected $table = 'encuestas_resultados';

    protected $fillable = [
        'encuesta_id',
        'nombre_estudiante',
        'edad_estudiante',
        'sexo_estudiante',
    ];

    protected $casts = [
        'edad_estudiante' => 'integer',
    ];

    /**
     * Relación con Encuesta
     */
    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class);
    }

    /**
     * Relación con respuestas
     */
    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaEncuesta::class);
    }

    /**
     * Relación con análisis TDA
     */
    public function analisisTda(): HasOne
    {
        return $this->hasOne(AnalisisTda::class);
    }

    /**
     * Obtiene todas las respuestas como array [pregunta_id => puntuacion]
     */
    public function obtenerRespuestasArray(): array
    {
        return $this->respuestas()
            ->pluck('puntuacion', 'pregunta_id')
            ->toArray();
    }
}
