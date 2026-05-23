<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encuesta extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'usuario_id'];

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con resultados
     */
    public function resultados(): HasMany
    {
        return $this->hasMany(EncuestaResultado::class);
    }

    /**
     * Relación muchos-a-muchos con Preguntas
     * Las preguntas asignadas a esta encuesta
     */
    public function preguntas(): BelongsToMany
    {
        return $this->belongsToMany(Pregunta::class, 'encuesta_pregunta')
            ->withPivot('orden')
            ->orderBy('encuesta_pregunta.orden');
    }

    /**
     * Obtiene las preguntas asignadas a esta encuesta
     * Formateadas como array para API
     */
    public function obtenerPreguntasTda(): array
    {
        $preguntasDb = $this->preguntas()->get();

        if ($preguntasDb->isEmpty()) {
            // Si no hay preguntas asignadas, retorna array vacío
            return [];
        }

        return $preguntasDb->map(function ($pregunta) {
            return [
                'id' => $pregunta->id,
                'category' => $pregunta->tipo_tda,
                'text' => $pregunta->nombre,
                'example' => $pregunta->ejemplo,
            ];
        })->toArray();
    }

    /**
     * Obtiene preguntas de esta encuesta o usa defaults si no hay asignadas
     */
    public function getPreguntasDisponibles(): array
    {
        $preguntas = $this->obtenerPreguntasTda();

        // Si no hay preguntas asignadas, usar las preguntas activas del sistema
        if (empty($preguntas)) {
            return app(\App\Services\TdaAnalysisService::class)->getAvailableQuestions();
        }

        return $preguntas;
    }
}
