<?php

namespace App\Services;

class TdaAnalysisService
{
    /**
     * Preguntas del cuestionario basadas en criterios DSM-5.
     * Categoría: I = Inatención, H = Hiperactividad/Impulsividad
     */
    public function getQuestions(): array
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
        $questions = collect($this->getQuestions())->keyBy('id');

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
        ];
    }
}
