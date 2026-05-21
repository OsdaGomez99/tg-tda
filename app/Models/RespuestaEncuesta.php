<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RespuestaEncuesta extends Model
{
    protected $table = 'respuestas_encuestas';

    protected $fillable = [
        'encuesta_resultado_id',
        'pregunta_id',
        'puntuacion',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    /**
     * Relación con EncuestaResultado
     */
    public function encuestaResultado(): BelongsTo
    {
        return $this->belongsTo(EncuestaResultado::class);
    }

    /**
     * Relación con Pregunta
     */
    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class);
    }
}
