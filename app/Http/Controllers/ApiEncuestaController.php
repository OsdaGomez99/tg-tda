<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\RespuestaEncuesta;
use App\Services\TdaAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiEncuestaController extends Controller
{
    public function __construct(private TdaAnalysisService $tdaService) {}

    /**
     * Verifica el código de acceso para peticiones no autenticadas del flujo público.
     */
    private function verificarAccesoPublico(Request $request, Encuesta $encuesta): void
    {
        if (Auth::check()) {
            return;
        }

        $codigoAcceso = $request->input('codigo_acceso');

        if (!$codigoAcceso || $codigoAcceso !== $encuesta->codigo_acceso) {
            abort(403, 'Código de acceso inválido.');
        }
    }

    /**
     * Obtiene una encuesta específica con sus preguntas asignadas
     */
    public function show(Request $request, Encuesta $encuesta): JsonResponse
    {
        $this->verificarAccesoPublico($request, $encuesta);

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
     * Guarda una respuesta individual
     */
    public function guardarRespuesta(Request $request, EncuestaResultado $resultado): JsonResponse
    {
        $encuesta = $resultado->encuesta;
        $this->verificarAccesoPublico($request, $encuesta);

        $validated = $request->validate([
            'pregunta_id' => 'required|integer|exists:preguntas,id',
            'puntuacion' => 'required|integer|min:0|max:3',
        ]);

        // Validar que la pregunta pertenece a esta encuesta
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
     * Finaliza la encuesta y genera el análisis de TDA
     */
    public function finalizar(Request $request, EncuestaResultado $resultado): JsonResponse
    {
        try {
            $encuesta = $resultado->encuesta;
            $this->verificarAccesoPublico($request, $encuesta);

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
}
