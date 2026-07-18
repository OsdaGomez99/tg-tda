<?php

namespace App\Services;

use App\Models\AnalisisTda;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;

class TdaAnalysisService
{
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
     * Analiza las respuestas y determina el tipo de TDA según criterios DSM-5.
     * Umbral clínico: ≥6 síntomas con puntuación ≥2 en cada categoría.
     *
     * @param  array $answers  [question_id => score (0-3)]
     * @param  Encuesta $encuesta
     * @return array
     */
    public function analyze(array $answers, Encuesta $encuesta): array
    {
        $questions = collect($encuesta->obtenerPreguntasTda())->keyBy('id');

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
                if ($score >= 2) {
                    $inattentionSymptoms++;
                }
            } else {
                $hyperactivityScore += $score;
                if ($score >= 2) {
                    $hyperactivitySymptoms++;
                }
            }
        }

        $totalScore = $inattentionScore + $hyperactivityScore;

        // Determinación del resultado según DSM-5
        $inattentiveSignificant   = $inattentionSymptoms >= 6;
        $hyperactiveSignificant   = $hyperactivitySymptoms >= 6;

        if ($inattentiveSignificant && $hyperactiveSignificant) {
            $result = 'tda_combinado';
        } elseif ($inattentiveSignificant) {
            $result = 'tda_inatento';
        } elseif ($hyperactiveSignificant) {
            $result = 'tda_hiperactivo';
        } elseif ($inattentionSymptoms >= 3 || $hyperactivitySymptoms >= 3) {
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

        // Realizar el análisis
        $analisisData = $this->analyze($respuestasGuardadas, $encuesta);
        // Guardar el resultado del análisis
        $analisisTda = AnalisisTda::create([
            'encuesta_resultado_id' => $resultado->id,
            'puntuacion_inatención' => $analisisData['inattention_score'],
            'puntuacion_hiperactividad' => $analisisData['hyperactivity_score'],
            'puntuacion_total' => $analisisData['total_score'],
            'sintomas_inatención' => $analisisData['inattention_symptoms'],
            'sintomas_hiperactividad' => $analisisData['hyperactivity_symptoms'],
            'resultado' => $analisisData['result'],
            'porcentaje_inatención' => $analisisData['inattention_percentage'],
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

        $descripcion = match ($result) {
            'tda_combinado' => sprintf(
                'TDA Combinado. Se han identificado síntomas significativos de inatención (%d síntomas, puntuación: %d/27) e hiperactividad/impulsividad (%d síntomas, puntuación: %d/27). Se recomienda evaluación profesional detallada.',
                $inattentionSymptoms,
                $inattentionScore,
                $hyperactivitySymptoms,
                $hyperactivityScore
            ),
            'tda_inatento' => sprintf(
                'TDA Tipo Inatento. Se han identificado %d síntomas significativos de inatención (puntuación: %d/27). Principales dificultades en concentración y atención sostenida. Se recomienda evaluación profesional.',
                $inattentionSymptoms,
                $inattentionScore
            ),
            'tda_hiperactivo' => sprintf(
                'TDA Tipo Hiperactivo/Impulsivo. Se han identificado %d síntomas significativos de hiperactividad e impulsividad (puntuación: %d/27). Se recomienda evaluación profesional.',
                $hyperactivitySymptoms,
                $hyperactivityScore
            ),
            'tda_posible' => sprintf(
                'Posible TDA. Se han identificado síntomas moderados (inatención: %d, hiperactividad: %d). Se recomienda seguimiento y evaluación adicional.',
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
            'promedio_inatención'    => round($analisisArray->avg('puntuacion_inatención'), 2),
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
