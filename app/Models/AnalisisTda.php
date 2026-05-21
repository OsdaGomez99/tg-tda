<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisisTda extends Model
{
    protected $table = 'analisis_tda';

    protected $fillable = [
        'encuesta_resultado_id',
        'puntuacion_inatención',
        'puntuacion_hiperactividad',
        'puntuacion_total',
        'sintomas_inatención',
        'sintomas_hiperactividad',
        'resultado',
        'porcentaje_inatención',
        'porcentaje_hiperactividad',
        'descripcion',
    ];

    protected $casts = [
        'puntuacion_inatención' => 'integer',
        'puntuacion_hiperactividad' => 'integer',
        'puntuacion_total' => 'integer',
        'sintomas_inatención' => 'integer',
        'sintomas_hiperactividad' => 'integer',
        'porcentaje_inatención' => 'float',
        'porcentaje_hiperactividad' => 'float',
    ];

    /**
     * Relación con EncuestaResultado
     */
    public function encuestaResultado(): BelongsTo
    {
        return $this->belongsTo(EncuestaResultado::class);
    }

    /**
     * Obtiene la descripción legible del resultado
     */
    public function getResultadoDescripcion(): string
    {
        return match ($this->resultado) {
            'tda_combinado' => 'TDA Combinado (Síntomas de Inatención e Hiperactividad)',
            'tda_inatento' => 'TDA Tipo Inatento (Dificultad de concentración)',
            'tda_hiperactivo' => 'TDA Tipo Hiperactivo/Impulsivo',
            'tda_possible' => 'Posible TDA (Síntomas moderados)',
            'no_tda' => 'No detectado TDA',
            default => 'Resultado desconocido'
        };
    }
}
