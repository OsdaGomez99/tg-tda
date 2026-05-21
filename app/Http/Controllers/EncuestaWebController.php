<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Services\TdaAnalysisService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EncuestaWebController extends Controller
{
    public function __construct(private TdaAnalysisService $tdaService) {}

    /**
     * Mostrar lista de encuestas disponibles
     */
    public function index(): View
    {
        $encuestas = Encuesta::with('usuario')->get();

        return view('pages.encuestas.encuestas-index', [
            'title' => 'Encuestas',
            'encuestas' => $encuestas
        ]);
    }

    /**
     * Mostrar formulario para iniciar encuesta
     */
    public function iniciar(Encuesta $encuesta): View
    {
        return view('pages.encuestas.encuestas-iniciar', [
            'title' => 'Iniciar Encuesta',
            'encuesta' => $encuesta
        ]);
    }

    /**
     * Guardar datos iniciales y redirigir a responder
     */
    public function store(Request $request, Encuesta $encuesta)
    {
        $validated = $request->validate([
            'nombre_estudiante' => 'required|string|max:255',
            'edad_estudiante' => 'required|integer|min:5|max:100',
            'sexo_estudiante' => 'required|in:M,F,O',
        ]);

        $resultado = EncuestaResultado::create(array_merge($validated, [
            'encuesta_id' => $encuesta->id,
        ]));

        return redirect()->route('responder-encuesta', [
            'resultado' => $resultado->id
        ]);
    }

    /**
     * Mostrar formulario de respuestas
     */
    public function responder(EncuestaResultado $resultado): View
    {
        $encuesta = $resultado->encuesta;

        return view('pages.encuestas.encuestas-responder', [
            'title' => 'Responder Encuesta',
            'encuesta' => $encuesta,
            'resultado' => $resultado
        ]);
    }

    /**
     * Mostrar resultado y análisis
     */
    public function resultado(EncuestaResultado $resultado): View
    {
        $analisis = $resultado->analisisTda;

        return view('pages.encuestas.encuestas-resultado', [
            'title' => 'Resultado de Encuesta',
            'resultado' => $resultado,
            'analisis' => $analisis
        ]);
    }

    /**
     * Mostrar detalles de respuestas
     */
    public function detalles(EncuestaResultado $resultado): View
    {
        $respuestas = $resultado->respuestas()->with('pregunta')->get();

        $analisis = $resultado->analisisTda;

        return view('pages.encuestas.encuestas-detalles', [
            'title' => 'Detalles de Respuestas',
            'resultado' => $resultado,
            'respuestas' => $respuestas,
            'analisis' => $analisis
        ]);
    }

    /**
     * Mostrar estadísticas de una encuesta
     */
    public function estadisticas(Encuesta $encuesta): View
    {
        $resultados = $encuesta->resultados()
            ->with(['analisisTda', 'respuestas'])
            ->get();

        if ($resultados->isEmpty()) {
            $estadisticas = [];
        } else {
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
        }

        return view('pages.encuestas.encuestas-estadisticas', [
            'title' => 'Estadísticas de Encuesta',
            'encuesta' => $encuesta,
            'resultados' => $resultados,
            'estadisticas' => $estadisticas
        ]);
    }
}
