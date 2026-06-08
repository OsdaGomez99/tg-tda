<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
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
        $carreras = Carrera::orderBy('nombre')->get();
        return view('pages.encuestas.encuestas-iniciar', [
            'title' => 'Iniciar Encuesta',
            'encuesta' => $encuesta,
            'carreras' => $carreras
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
            'carrera_id' => 'nullable|exists:carreras,id',
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

        // Delegar cálculo al servicio, igual que ApiEncuestaController
        $estadisticas = $this->tdaService->calcularEstadisticas($resultados);

        return view('pages.encuestas.encuestas-estadisticas', [
            'title'       => 'Estadísticas de Encuesta',
            'encuesta'    => $encuesta,
            'resultados'  => $resultados,
            'estadisticas' => $estadisticas,
        ]);
    }
}
