<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisisTda extends Model
{
    protected $table = 'analisis_tda';

    protected $fillable = [
        'encuesta_resultado_id',
        'puntuacion_inatencion',
        'puntuacion_hiperactividad',
        'puntuacion_total',
        'sintomas_inatencion',
        'sintomas_hiperactividad',
        'umbral_sintomas',
        'resultado',
        'porcentaje_inatencion',
        'porcentaje_hiperactividad',
        'descripcion',
    ];

    protected $casts = [
        'puntuacion_inatencion' => 'integer',
        'puntuacion_hiperactividad' => 'integer',
        'puntuacion_total' => 'integer',
        'sintomas_inatencion' => 'integer',
        'sintomas_hiperactividad' => 'integer',
        'umbral_sintomas' => 'integer',
        'porcentaje_inatencion' => 'float',
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
            'tda_posible' => 'Posible TDA (Síntomas moderados)',
            'no_tda' => 'No detectado TDA',
            default => 'Resultado desconocido'
        };
    }

    /**
     * Describe el criterio DSM-5 aplicado en este análisis.
     */
    public function getCriterioAplicado(): string
    {
        return sprintf(
            '%d o más síntomas por dimensión (DSM-5, %s)',
            $this->umbral_sintomas,
            $this->umbral_sintomas === 5 ? '17 años o más' : 'menores de 17 años'
        );
    }

    /**
     * Obtiene las recomendaciones de orientación académica según el tipo de resultado
     */
    public function getRecomendaciones(): array
    {
        return match ($this->resultado) {
            'no_tda' => [
                'Planificación semanal: Dedica 15 minutos al inicio de la semana para calendarizar tus entregas.',
                'Descansos activos: Implementa pausas de 5 minutos por cada hora de estudio continuo para evitar la fatiga mental.',
            ],
            'tda_inatento' => [
                'Método Pomodoro (25/5): Estudia en bloques cerrados de 25 minutos con temporizador y descansa 5 minutos. Evita las jornadas maratónicas.',
                'Control estricto de estímulos: Retira el teléfono de tu campo visual y usa extensiones en el navegador para bloquear redes sociales mientras estudias.',
                'Segmentación de tareas: Divide los proyectos complejos en micro-tareas diarias de 15 minutos.',
            ],
            'tda_hiperactivo' => [
                'Estudio en movimiento / cambios de entorno: Incorpora el uso de escritorios de pie si es posible, o alterna tus lugares de estudio.',
                'Canalización física previa: Realiza una actividad física ligera o caminata corta de 10 minutos antes de sentarte a procesar lecturas complejas.',
                'Técnicas de estudio activo: Evita la lectura pasiva. Utiliza mapas mentales, explica la materia en voz alta o escribe notas breves.',
            ],
            'tda_combinado' => [
                'Listas de tareas prioritarias (Regla de 3): Anota solo 3 actividades cruciales al inicio del día.',
                'Asistentes visuales y recordatorios: Utiliza alarmas sonoras o tableros visuales físicos en tu área de estudio.',
                'Entornos libres de interrupciones: Busca espacios de alta estructura para tus horas de mayor exigencia académica.',
            ],
            'tda_posible' => [
                'Uso de agendas o recordatorios digitales: Apóyate en herramientas como Google Calendar o Notion.',
                'Listas de tareas diarias: Anota un máximo de 3 actividades clave para mitigar la procrastinación.',
                'Auto-monitoreo de distracciones: Identifica y reduce tus mayores distractores durante los bloques de estudio.',
            ],
            default => [],
        };
    }
}
