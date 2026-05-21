<?php

namespace App\Services;

use App\Models\AnalisisTda;
use App\Models\EncuestaResultado;
use App\Models\Pregunta;
use App\Models\RespuestaEncuesta;
use Illuminate\Database\Eloquent\Collection;

class TdaAnalysisService
{
    /**
     * Obtiene las preguntas del cuestionario desde la BD
     * Las preguntas deben estar marcadas con tipo_tda (I o H)
     */
    public function getQuestions(): array
    {
        $preguntas = Pregunta::where('estado', true)
            ->whereNotNull('tipo_tda')
            ->orderBy('id')
            ->get();

        return $preguntas->map(function ($pregunta) {
            return [
                'id'       => $pregunta->id,
                'category' => $pregunta->tipo_tda,
                'text'     => $pregunta->nombre,
                'example'  => $pregunta->ejemplo,
            ];
        })->toArray();
    }

    /**
     * Preguntas de referencia DSM-5 (backup en caso de que la BD esté vacía)
     * Categoría: I = Inatención, H = Hiperactividad/Impulsividad
     */
    public function getDefaultQuestions(): array
    {
        return [
            // Inatención (9 síntomas DSM-5)
            [
                'id'       => 1,
                'category' => 'I',
                'text'     => 'Con frecuencia no presta atención suficiente a los detalles o comete errores por descuido en las tareas o en otras actividades.',
                'example'  => 'Ej: pasa por alto detalles, el trabajo es impreciso.',
            ],
            [
                'id'       => 2,
                'category' => 'I',
                'text'     => 'Con frecuencia tiene dificultades para mantener la atención en tareas o actividades recreativas.',
                'example'  => 'Ej: le cuesta mantenerse enfocado durante una clase o conversación larga.',
            ],
            [
                'id'       => 3,
                'category' => 'I',
                'text'     => 'Con frecuencia parece no escuchar cuando se le habla directamente.',
                'example'  => 'Ej: su mente parece estar en otro lugar, incluso sin ninguna distracción aparente.',
            ],
            [
                'id'       => 4,
                'category' => 'I',
                'text'     => 'Con frecuencia no sigue las instrucciones y no termina las tareas escolares, los quehaceres o las responsabilidades laborales.',
                'example'  => 'Ej: empieza tareas pero se distrae rápidamente.',
            ],
            [
                'id'       => 5,
                'category' => 'I',
                'text'     => 'Con frecuencia tiene dificultades para organizar tareas y actividades.',
                'example'  => 'Ej: le cuesta gestionar tareas secuenciales o mantener el orden en sus cosas.',
            ],
            [
                'id'       => 6,
                'category' => 'I',
                'text'     => 'Con frecuencia evita, le disgusta o es renuente a dedicarse a tareas que requieren un esfuerzo mental sostenido.',
                'example'  => 'Ej: deberes escolares o trabajo administrativo.',
            ],
            [
                'id'       => 7,
                'category' => 'I',
                'text'     => 'Con frecuencia pierde objetos necesarios para tareas o actividades.',
                'example'  => 'Ej: pierde materiales escolares, llaves, lentes, teléfono.',
            ],
            [
                'id'       => 8,
                'category' => 'I',
                'text'     => 'Con frecuencia se distrae fácilmente por estímulos externos.',
                'example'  => 'Ej: pensamientos no relacionados o ruidos del entorno.',
            ],
            [
                'id'       => 9,
                'category' => 'I',
                'text'     => 'Con frecuencia es olvidadizo en las actividades cotidianas.',
                'example'  => 'Ej: hacer las tareas, las diligencias, responder llamadas.',
            ],

            // Hiperactividad / Impulsividad (9 síntomas DSM-5)
            [
                'id'       => 10,
                'category' => 'H',
                'text'     => 'Con frecuencia juguetea con o golpea las manos o los pies, o se retuerce en el asiento.',
                'example'  => 'Ej: no puede estar quieto en el asiento.',
            ],
            [
                'id'       => 11,
                'category' => 'H',
                'text'     => 'Con frecuencia se levanta en situaciones en que se espera que permanezca sentado.',
                'example'  => 'Ej: se levanta en el aula o en la oficina.',
            ],
            [
                'id'       => 12,
                'category' => 'H',
                'text'     => 'Con frecuencia corretea o trepa en situaciones en las que no resulta apropiado.',
                'example'  => 'Nota: en adultos puede limitarse a una sensación de inquietud.',
            ],
            [
                'id'       => 13,
                'category' => 'H',
                'text'     => 'Con frecuencia es incapaz de jugar o de ocuparse tranquilamente en actividades recreativas.',
                'example'  => 'Ej: le cuesta actividades tranquilas y sosegadas.',
            ],
            [
                'id'       => 14,
                'category' => 'H',
                'text'     => 'Con frecuencia está "ocupado", actuando como si "lo impulsara un motor".',
                'example'  => 'Ej. es incapaz de estar quieto durante mucho tiempo.',
            ],
            [
                'id'       => 15,
                'category' => 'H',
                'text'     => 'Con frecuencia habla excesivamente.',
                'example'  => 'Ej: habla sin parar en situaciones sociales o de trabajo.',
            ],
            [
                'id'       => 16,
                'category' => 'H',
                'text'     => 'Con frecuencia responde inesperadamente o antes de que se haya concluido una pregunta.',
                'example'  => 'Ej: termina la frase de los demás.',
            ],
            [
                'id'       => 17,
                'category'  => 'H',
                'text'     => 'Con frecuencia le es difícil esperar su turno.',
                'example'  => 'Ej. mientras espera en una fila.',
            ],
            [
                'id'       => 18,
                'category' => 'H',
                'text'     => 'Con frecuencia interrumpe o se inmiscuye con otros.',
                'example'  => 'Ej: se entromete en conversaciones, juegos o actividades.',
            ],
        ];
    }

    /**
     * Obtiene preguntas, usando BD si está disponible o defaults si no
     */
    public function getAvailableQuestions(): array
    {
        $questions = $this->getQuestions();
        return count($questions) > 0 ? $questions : $this->getDefaultQuestions();
    }

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
     * @return array
     */
    public function analyze(array $answers): array
    {
        $questions = collect($this->getAvailableQuestions())->keyBy('id');

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
            $result = 'tda_possible';
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
        // Obtener respuestas ya guardadas
        $respuestasGuardadas = $resultado->obtenerRespuestasArray();

        // Realizar el análisis
        $analisisData = $this->analyze($respuestasGuardadas);
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
     * Alias del método generarAnalisis para compatibilidad
     * @deprecated Use generarAnalisis() instead
     */
    public function guardarRespuestasYAnalizar(EncuestaResultado $resultado, array $answers = []): AnalisisTda
    {
        return $this->generarAnalisis($resultado);
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
                'Resultado: TDA Combinado. Se han identificado síntomas significativos de inatención (%d síntomas, puntuación: %d/27) e hiperactividad/impulsividad (%d síntomas, puntuación: %d/27). Se recomienda evaluación profesional detallada.',
                $inattentionSymptoms,
                $inattentionScore,
                $hyperactivitySymptoms,
                $hyperactivityScore
            ),
            'tda_inatento' => sprintf(
                'Resultado: TDA Tipo Inatento. Se han identificado %d síntomas significativos de inatención (puntuación: %d/27). Principales dificultades en concentración y atención sostenida. Se recomienda evaluación profesional.',
                $inattentionSymptoms,
                $inattentionScore
            ),
            'tda_hiperactivo' => sprintf(
                'Resultado: TDA Tipo Hiperactivo/Impulsivo. Se han identificado %d síntomas significativos de hiperactividad e impulsividad (puntuación: %d/27). Se recomienda evaluación profesional.',
                $hyperactivitySymptoms,
                $hyperactivityScore
            ),
            'tda_possible' => sprintf(
                'Resultado: Posible TDA. Se han identificado síntomas moderados (inatención: %d, hiperactividad: %d). Se recomienda seguimiento y evaluación adicional.',
                $inattentionSymptoms,
                $hyperactivitySymptoms
            ),
            'no_tda' => sprintf(
                'Resultado: No se detectan síntomas clínicamente significativos de TDA. Puntuaciones de inatención: %d/27, Hiperactividad: %d/27. Perfil dentro de los parámetros típicos.',
                $inattentionScore,
                $hyperactivityScore
            ),
            default => 'Análisis completado.'
        };

        return $descripcion;
    }

    /**
     * Obtiene todas las respuestas de un resultado de encuesta
     */
    public function obtenerRespuestas(EncuestaResultado $resultado): Collection
    {
        return $resultado->respuestas()
            ->with('pregunta')
            ->get();
    }

    /**
     * Obtiene el análisis de un resultado
     */
    public function obtenerAnalisis(EncuestaResultado $resultado): ?AnalisisTda
    {
        return $resultado->analisisTda;
    }

    /**
     * Exporta los resultados en formato array para API
     */
    public function exportarResultado(EncuestaResultado $resultado): array
    {
        $analisis = $this->obtenerAnalisis($resultado);
        $respuestas = $this->obtenerRespuestas($resultado);

        return [
            'resultado' => [
                'id' => $resultado->id,
                'nombre_estudiante' => $resultado->nombre_estudiante,
                'edad_estudiante' => $resultado->edad_estudiante,
                'sexo_estudiante' => $resultado->sexo_estudiante,
                'created_at' => $resultado->created_at,
            ],
            'analisis' => $analisis ? [
                'resultado' => $analisis->resultado,
                'puntuacion_inatención' => $analisis->puntuacion_inatención,
                'puntuacion_hiperactividad' => $analisis->puntuacion_hiperactividad,
                'puntuacion_total' => $analisis->puntuacion_total,
                'sintomas_inatención' => $analisis->sintomas_inatención,
                'sintomas_hiperactividad' => $analisis->sintomas_hiperactividad,
                'porcentaje_inatención' => $analisis->porcentaje_inatención,
                'porcentaje_hiperactividad' => $analisis->porcentaje_hiperactividad,
                'descripcion' => $analisis->descripcion,
                'descripcion_resultado' => $analisis->getResultadoDescripcion(),
            ] : null,
            'respuestas_count' => $respuestas->count(),
        ];
    }
}
