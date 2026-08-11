<?php

namespace App\Services;

use App\Models\AnalisisTda;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;

class TdaAnalysisService
{
    /**
     * Edad a partir de la cual el DSM-5 reduce el umbral diagnóstico.
     * DSM-5, Criterio A: "For older adolescents and adults (age 17 and older),
     * at least five symptoms are required".
     */
    public const EDAD_ADULTO_DSM5 = 17;

    /** Síntomas mínimos por dimensión en personas de 17 años o más. */
    public const UMBRAL_SINTOMAS_ADULTO = 5;

    /** Síntomas mínimos por dimensión en menores de 17 años. */
    public const UMBRAL_SINTOMAS_MENOR = 6;

    /**
     * Síntomas mínimos para clasificar un perfil como "posible TDA".
     * Criterio propio de la aplicación (no proviene del DSM-5): identifica
     * perfiles subumbral que ameritan seguimiento sin alcanzar significación.
     */
    public const UMBRAL_SINTOMAS_POSIBLE = 3;

    /** Puntuación Likert mínima para contabilizar un ítem como síntoma presente. */
    public const PUNTUACION_SINTOMA_PRESENTE = 2;

    /**
     * Opciones de respuesta con puntuación (escala Likert 0-3).
     */
    public function getResponseOptions(): array
    {
        return [
            0 => 'Nunca o raramente',
            1 => 'A veces',
            2 => 'Con frecuencia',
            3 => 'Muy frecuentemente',
        ];
    }

    /**
     * Determina el número mínimo de síntomas por dimensión exigido por el DSM-5
     * según la edad del participante.
     *
     * Ante una edad desconocida se aplica el umbral más exigente (6 síntomas),
     * criterio conservador que evita sobreestimar la detección.
     *
     * @param  int|null  $edad  Edad declarada por el participante.
     * @return int
     */
    public function umbralSintomas(?int $edad): int
    {
        if ($edad !== null && $edad >= self::EDAD_ADULTO_DSM5) {
            return self::UMBRAL_SINTOMAS_ADULTO;
        }

        return self::UMBRAL_SINTOMAS_MENOR;
    }

    /**
     * Analiza las respuestas y determina el tipo de TDA según criterios DSM-5.
     *
     * Umbral clínico dependiente de la edad: 5 síntomas por dimensión desde los
     * 17 años, 6 síntomas en menores de esa edad. Un ítem se contabiliza como
     * síntoma cuando su puntuación es ≥ 2 en la escala Likert 0-3.
     *
     * @param  array     $answers   [question_id => score (0-3)]
     * @param  Encuesta  $encuesta
     * @param  int|null  $edad      Edad del participante.
     * @return array
     */
    public function analyze(array $answers, Encuesta $encuesta, ?int $edad = null): array
    {
        $questions = collect($encuesta->obtenerPreguntasTda())->keyBy('id');
        $umbral    = $this->umbralSintomas($edad);

        if ($questions->isEmpty()) {
            return [
                'result' => 'no_tda',
                'inattention_score' => 0,
                'hyperactivity_score' => 0,
                'total_score' => 0,
                'inattention_symptoms' => 0,
                'hyperactivity_symptoms' => 0,
                'max_inattention_score' => 27,
                'max_hyperactivity_score' => 27,
                'max_total_score' => 54,
                'inattention_percentage' => 0,
                'hyperactivity_percentage' => 0,
                'symptom_threshold' => $umbral,
                'age' => $edad,
            ];
        }

        $inattentionScore      = 0;
        $hyperactivityScore    = 0;
        $inattentionSymptoms   = 0;   // síntomas con puntaje ≥ 2
        $hyperactivitySymptoms = 0;

        foreach ($answers as $questionId => $score) {
            $score = (int) $score;
            $question = $questions->get($questionId);

            if (! $question) {
                continue;
            }

            if ($question['category'] === 'I') {
                $inattentionScore += $score;
                if ($score >= self::PUNTUACION_SINTOMA_PRESENTE) {
                    $inattentionSymptoms++;
                }
            } else {
                $hyperactivityScore += $score;
                if ($score >= self::PUNTUACION_SINTOMA_PRESENTE) {
                    $hyperactivitySymptoms++;
                }
            }
        }

        $totalScore = $inattentionScore + $hyperactivityScore;

        // Determinación del resultado según DSM-5 (umbral ajustado por edad)
        $inattentiveSignificant = $inattentionSymptoms >= $umbral;
        $hyperactiveSignificant = $hyperactivitySymptoms >= $umbral;

        if ($inattentiveSignificant && $hyperactiveSignificant) {
            $result = 'tda_combinado';
        } elseif ($inattentiveSignificant) {
            $result = 'tda_inatento';
        } elseif ($hyperactiveSignificant) {
            $result = 'tda_hiperactivo';
        } elseif (
            $inattentionSymptoms >= self::UMBRAL_SINTOMAS_POSIBLE
            || $hyperactivitySymptoms >= self::UMBRAL_SINTOMAS_POSIBLE
        ) {
            $result = 'tda_posible';
        } else {
            $result = 'no_tda';
        }

        return [
            'result'                  => $result,
            'inattention_score'       => $inattentionScore,
            'hyperactivity_score'     => $hyperactivityScore,
            'total_score'             => $totalScore,
            'inattention_symptoms'    => $inattentionSymptoms,
            'hyperactivity_symptoms'  => $hyperactivitySymptoms,
            'max_inattention_score'   => 27,
            'max_hyperactivity_score' => 27,
            'max_total_score'         => 54,
            'inattention_percentage'  => round(($inattentionScore / 27) * 100, 2),
            'hyperactivity_percentage' => round(($hyperactivityScore / 27) * 100, 2),
            'symptom_threshold'       => $umbral,
            'age'                     => $edad,
        ];
    }

    /**
     * Genera el análisis basado en respuestas existentes
     * Las respuestas ya deben estar guardadas en la BD
     *
     * @param EncuestaResultado $resultado
     * @return AnalisisTda
     */
    public function generarAnalisis(EncuestaResultado $resultado): AnalisisTda
    {
        $encuesta = $resultado->encuesta;

        // Obtener respuestas ya guardadas
        $respuestasGuardadas = $resultado->obtenerRespuestasArray();

        // Realizar el análisis (el umbral se resuelve con la edad declarada)
        $analisisData = $this->analyze(
            $respuestasGuardadas,
            $encuesta,
            $resultado->edad_estudiante
        );

        // Guardar el resultado del análisis
        $analisisTda = AnalisisTda::create([
            'encuesta_resultado_id' => $resultado->id,
            'puntuacion_inatencion' => $analisisData['inattention_score'],
            'puntuacion_hiperactividad' => $analisisData['hyperactivity_score'],
            'puntuacion_total' => $analisisData['total_score'],
            'sintomas_inatencion' => $analisisData['inattention_symptoms'],
            'sintomas_hiperactividad' => $analisisData['hyperactivity_symptoms'],
            'umbral_sintomas' => $analisisData['symptom_threshold'],
            'resultado' => $analisisData['result'],
            'porcentaje_inatencion' => $analisisData['inattention_percentage'],
            'porcentaje_hiperactividad' => $analisisData['hyperactivity_percentage'],
            'descripcion' => $this->generarDescripcion($analisisData),
        ]);

        return $analisisTda;
    }

    /**
     * Genera una descripción textual del resultado
     */
    private function generarDescripcion(array $analisisData): string
    {
        $result = $analisisData['result'];
        $inattentionScore = $analisisData['inattention_score'];
        $hyperactivityScore = $analisisData['hyperactivity_score'];
        $inattentionSymptoms = $analisisData['inattention_symptoms'];
        $hyperactivitySymptoms = $analisisData['hyperactivity_symptoms'];
        $umbral = $analisisData['symptom_threshold'] ?? self::UMBRAL_SINTOMAS_MENOR;

        $descripcion = match ($result) {
            'tda_combinado' => sprintf(
                'Se han identificado síntomas significativos de inatención (%d síntomas, puntuación: %d/27) e hiperactividad/impulsividad (%d síntomas, puntuación: %d/27). Se recomienda evaluación profesional detallada.',
                $inattentionSymptoms,
                $inattentionScore,
                $hyperactivitySymptoms,
                $hyperactivityScore
            ),
            'tda_inatento' => sprintf(
                'Se han identificado %d síntomas significativos de inatención (puntuación: %d/27). Principales dificultades en concentración y atención sostenida. Se recomienda evaluación profesional.',
                $inattentionSymptoms,
                $inattentionScore
            ),
            'tda_hiperactivo' => sprintf(
                'Se han identificado %d síntomas significativos de hiperactividad e impulsividad (puntuación: %d/27). Se recomienda evaluación profesional.',
                $hyperactivitySymptoms,
                $hyperactivityScore
            ),
            'tda_posible' => sprintf(
                'Se han identificado síntomas moderados (inatención: %d, hiperactividad: %d). Se recomienda seguimiento y evaluación adicional.',
                $inattentionSymptoms,
                $hyperactivitySymptoms
            ),
            'no_tda' => sprintf(
                'No se detectan síntomas clínicamente significativos de TDA. Puntuaciones de inatención: %d/27, Hiperactividad: %d/27. Perfil dentro de los parámetros típicos.',
                $inattentionScore,
                $hyperactivityScore
            ),
            default => 'Análisis completado.'
        };

        $descripcion .= sprintf(
            ' Criterio aplicado: %d o más síntomas por dimensión con frecuencia igual o superior a "Con frecuencia" (DSM-5, umbral correspondiente a %s).',
            $umbral,
            $umbral === self::UMBRAL_SINTOMAS_ADULTO
                ? 'personas de 17 años o más'
                : 'menores de 17 años'
        );

        return $descripcion;
    }

    public function calcularEstadisticas($resultados): array
    {
        if ($resultados->isEmpty()) {
            return [];
        }

        $analisisArray = $resultados->map->analisisTda->filter();

        return [
            'total_respondientes'    => $resultados->count(),
            'resultados_completados' => $analisisArray->count(),
            'distribucion_resultados' => [
                'tda_combinado'  => $analisisArray->where('resultado', 'tda_combinado')->count(),
                'tda_inatento'   => $analisisArray->where('resultado', 'tda_inatento')->count(),
                'tda_hiperactivo' => $analisisArray->where('resultado', 'tda_hiperactivo')->count(),
                'tda_posible'   => $analisisArray->where('resultado', 'tda_posible')->count(),
                'no_tda'         => $analisisArray->where('resultado', 'no_tda')->count(),
            ],
            'promedio_inatencion'    => round($analisisArray->avg('puntuacion_inatencion'), 2),
            'promedio_hiperactividad' => round($analisisArray->avg('puntuacion_hiperactividad'), 2),
            'promedio_total'         => round($analisisArray->avg('puntuacion_total'), 2),
            'edad_promedio'          => round($resultados->avg('edad_estudiante'), 1),
            'distribucion_genero'    => [
                'M' => $resultados->where('sexo_estudiante', 'M')->count(),
                'F' => $resultados->where('sexo_estudiante', 'F')->count(),
                'O' => $resultados->where('sexo_estudiante', 'O')->count(),
            ],
        ];
    }
}
