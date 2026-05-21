<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\RespuestaEncuesta;
use App\Services\TdaAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiEncuestaController extends Controller
{
    public function __construct(private TdaAnalysisService $tdaService) {}

    /**
     * Obtiene todas las encuestas disponibles
     */
    public function index(): JsonResponse
    {
        $encuestas = Encuesta::with('usuario')->get();

        return response()->json([
            'success' => true,
            'data' => $encuestas,
        ]);
    }

    /**
     * Obtiene una encuesta específica con sus preguntas asignadas
     */
    public function show(Encuesta $encuesta): JsonResponse
    {
        $preguntas = $encuesta->getPreguntasDisponibles();
        $opciones = $this->tdaService->getResponseOptions();

        return response()->json([
            'success' => true,
            'encuesta' => $encuesta,
            'preguntas' => $preguntas,
            'opciones_respuesta' => $opciones,
            'total_preguntas' => count($preguntas),
        ]);
    }

    /**
     * Inicia una nueva respuesta de encuesta
     */
    public function iniciar(Request $request, Encuesta $encuesta): JsonResponse
    {
        $validated = $request->validate([
            'nombre_estudiante' => 'required|string|max:255',
            'edad_estudiante' => 'required|integer|min:5|max:100',
            'sexo_estudiante' => 'required|in:M,F,O',
        ]);

        $resultado = EncuestaResultado::create(array_merge($validated, [
            'encuesta_id' => $encuesta->id,
        ]));

        $encuesta = Encuesta::findOrFail($resultado->encuesta_id);
        $preguntas = $encuesta->getPreguntasDisponibles();
        $opciones = $this->tdaService->getResponseOptions();

        return response()->json([
            'success' => true,
            'message' => 'Encuesta iniciada correctamente',
            'resultado_id' => $resultado->id,
            'encuesta' => $encuesta,
            'preguntas' => $preguntas,
            'opciones_respuesta' => $opciones,
        ], 201);
    }

    /**
     * Guarda una respuesta individual
     */
    public function guardarRespuesta(Request $request, EncuestaResultado $resultado): JsonResponse
    {
        $validated = $request->validate([
            'pregunta_id' => 'required|integer|exists:preguntas,id',
            'puntuacion' => 'required|integer|min:0|max:3',
        ]);

        // Validar que la pregunta pertenece a esta encuesta
        $encuesta = $resultado->encuesta;
        $preguntasEncuesta = collect($encuesta->getPreguntasDisponibles())->pluck('id');

        if (!$preguntasEncuesta->contains($validated['pregunta_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'La pregunta no pertenece a esta encuesta',
            ], 400);
        }

        // Guardar respuesta
        $respuesta = RespuestaEncuesta::updateOrCreate(
            [
                'encuesta_resultado_id' => $resultado->id,
                'pregunta_id' => $validated['pregunta_id'],
            ],
            ['puntuacion' => $validated['puntuacion']]
        );

        // Verificar progreso
        $totalPreguntas = count($encuesta->getPreguntasDisponibles());
        $respuestasGuardadas = $resultado->respuestas()->count();

        return response()->json([
            'success' => true,
            'message' => 'Respuesta guardada correctamente',
            'respuesta' => $respuesta,
            'progreso' => [
                'total' => $totalPreguntas,
                'respondidas' => $respuestasGuardadas,
                'faltantes' => $totalPreguntas - $respuestasGuardadas,
                'porcentaje' => round(($respuestasGuardadas / $totalPreguntas) * 100, 2),
            ],
        ]);
    }

    /**
     * Guarda múltiples respuestas a la vez
     */
    public function guardarRespuestas(Request $request, EncuestaResultado $resultado): JsonResponse
    {
        $validated = $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*.pregunta_id' => 'required|integer|exists:preguntas,id',
            'respuestas.*.puntuacion' => 'required|integer|min:0|max:3',
        ]);

        try {
            $encuesta = $resultado->encuesta;
            $preguntasEncuesta = collect($encuesta->getPreguntasDisponibles())->pluck('id');

            // Validar que todas las preguntas pertenecen a la encuesta
            foreach ($validated['respuestas'] as $respuestaData) {
                if (!$preguntasEncuesta->contains($respuestaData['pregunta_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Una o más preguntas no pertenecen a esta encuesta',
                    ], 400);
                }
            }

            // Guardar respuestas
            foreach ($validated['respuestas'] as $respuestaData) {
                RespuestaEncuesta::updateOrCreate(
                    [
                        'encuesta_resultado_id' => $resultado->id,
                        'pregunta_id' => $respuestaData['pregunta_id'],
                    ],
                    ['puntuacion' => $respuestaData['puntuacion']]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Respuestas guardadas correctamente',
                'respuestas_guardadas' => count($validated['respuestas']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar las respuestas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Finaliza la encuesta y genera el análisis de TDA
     */
    public function finalizar(EncuestaResultado $resultado): JsonResponse
    {
        try {
            $encuesta = $resultado->encuesta;

            // Obtener respuestas
            $respuestas = $resultado->obtenerRespuestasArray();

            // Verificar que hay todas las respuestas esperadas
            $totalPreguntas = count($encuesta->getPreguntasDisponibles());
            if (count($respuestas) < $totalPreguntas) {
                return response()->json([
                    'success' => false,
                    'message' => "Faltan respuestas. Se esperaban {$totalPreguntas}, pero solo hay " . count($respuestas),
                ], 400);
            }

            // Generar análisis
            $analisis = $this->tdaService->generarAnalisis($resultado);

            return response()->json([
                'success' => true,
                'message' => 'Encuesta finalizada correctamente',
                'analisis' => $analisis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar la encuesta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene el resultado y análisis de una encuesta específica
     */
    public function obtenerResultado(EncuestaResultado $resultado): JsonResponse
    {
        $analisis = $this->tdaService->obtenerAnalisis($resultado);

        if (!$analisis) {
            return response()->json([
                'success' => false,
                'message' => 'No se ha completado el análisis para esta encuesta',
            ], 404);
        }

        $resultadoExportado = $this->tdaService->exportarResultado($resultado);

        return response()->json([
            'success' => true,
            'data' => $resultadoExportado,
        ]);
    }

    /**
     * Obtiene todos los resultados de una encuesta
     */
    public function obtenerResultados(Encuesta $encuesta): JsonResponse
    {
        $resultados = $encuesta->resultados()
            ->with(['analisisTda', 'respuestas'])
            ->get()
            ->map(function ($resultado) {
                return $this->tdaService->exportarResultado($resultado);
            });

        return response()->json([
            'success' => true,
            'encuesta' => $encuesta->only(['id', 'nombre']),
            'total_respondientes' => $resultados->count(),
            'resultados' => $resultados,
        ]);
    }

    /**
     * Obtiene estadísticas de una encuesta
     */
    public function estadisticas(Encuesta $encuesta): JsonResponse
    {
        $resultados = $encuesta->resultados()
            ->with('analisisTda')
            ->get();

        if ($resultados->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No hay resultados para esta encuesta',
                'estadisticas' => [],
            ]);
        }

        $analisisArray = $resultados->map->analisisTda->filter();

        $estadisticas = [
            'total_respondientes' => $resultados->count(),
            'resultados_completados' => $analisisArray->count(),
            'distribucion_resultados' => [
                'tda_combinado' => $analisisArray->where('resultado', 'tda_combinado')->count(),
                'tda_inatento' => $analisisArray->where('resultado', 'tda_inatento')->count(),
                'tda_hiperactivo' => $analisisArray->where('resultado', 'tda_hiperactivo')->count(),
                'tda_possible' => $analisisArray->where('resultado', 'tda_possible')->count(),
                'no_tda' => $analisisArray->where('resultado', 'no_tda')->count(),
            ],
            'promedio_inatención' => round($analisisArray->avg('puntuacion_inatención'), 2),
            'promedio_hiperactividad' => round($analisisArray->avg('puntuacion_hiperactividad'), 2),
            'promedio_total' => round($analisisArray->avg('puntuacion_total'), 2),
            'edad_promedio' => round($resultados->avg('edad_estudiante'), 1),
            'distribucion_genero' => [
                'M' => $resultados->where('sexo_estudiante', 'M')->count(),
                'F' => $resultados->where('sexo_estudiante', 'F')->count(),
                'O' => $resultados->where('sexo_estudiante', 'O')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'encuesta' => $encuesta->only(['id', 'nombre']),
            'estadisticas' => $estadisticas,
        ]);
    }
}
