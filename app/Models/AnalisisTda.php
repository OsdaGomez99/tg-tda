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
        'umbral_sintomas',
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
        'umbral_sintomas' => 'integer',
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
                'Planificación Semanal: dedica 15 minutos al inicio de la semana para calendarizar tus entregas.',
                'Descansos Activos: implementa pausas de 5 minutos por cada hora de estudio continuo para evitar la fatiga mental.',
            ],
            'tda_inatento' => [
                'Método Pomodoro (25/5): estudia en bloques cerrados de 25 minutos con temporizador y descansa 5 minutos. Evita las jornadas maratónicas.',
                'Control Estricto de Estímulos: retira el teléfono de tu campo visual y bloquea redes sociales mientras estudias.',
                'Segmentación de Tareas: divide los proyectos complejos en micro-tareas diarias de 15 minutos.',
            ],
            'tda_hiperactivo' => [
                'Estudio en Movimiento / Cambios de Entorno: incorpora escritorios de pie si es posible, o alterna tus lugares de estudio.',
                'Canalización Física Previa: realiza una actividad física ligera o caminata corta antes de procesar lecturas complejas.',
                'Técnicas de Estudio Activo: evita la lectura pasiva; usa mapas mentales, explica la materia en voz alta o escribe notas breves.',
            ],
            'tda_combinado' => [
                'Listas de Tareas Prioritarias (Regla de 3): anota solo 3 actividades cruciales al inicio del día.',
                'Asistentes Visuales y Recordatorios: utiliza alarmas sonoras o tableros visuales físicos en tu área de estudio.',
                'Entornos Libres de Interrupciones: busca espacios de alta estructura para tus horas de mayor exigencia académica.',
            ],
            'tda_posible' => [
                'Uso de Agendas o Recordatorios Digitales: apóyate en herramientas como Google Calendar o Notion.',
                'Listas de Tareas Diarias: anota un máximo de 3 actividades clave para mitigar la procrastinación.',
                'Auto-monitoreo de Distracciones: identifica y reduce tus mayores distractores durante los bloques de estudio.',
            ],
            default => [],
        };
    }
}
