<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Services\TdaAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

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
    public function iniciarPublic(string $codigo_acceso): View
    {
        $encuesta = $this->obtenerEncuestaPorCodigo($codigo_acceso);

        return $this->mostrarFormularioInicio($encuesta);
    }

    /**
     * Guardar datos iniciales y redirigir a responder
     */
    public function storePublic(Request $request, string $codigo_acceso)
    {
        $encuesta = $this->obtenerEncuestaPorCodigo($codigo_acceso);

        return $this->guardarDatosIniciales($request, $encuesta);
    }

    /**
     * Mostrar formulario de respuestas
     */
    public function responderPublic(string $codigo_acceso, string $resultado): View
    {
        $encuesta = $this->obtenerEncuestaPorCodigo($codigo_acceso);
        $resultadoId = $this->decodeResultadoId($resultado);
        $resultado = EncuestaResultado::findOrFail($resultadoId);

        if ($resultado->encuesta_id !== $encuesta->id) {
            abort(404);
        }

        return $this->mostrarFormularioRespuesta($resultado);
    }

    /**
     * Mostrar resultado y análisis
     */
    public function resultado(EncuestaResultado $resultado): View
    {
        return $this->mostrarResultado($resultado);
    }

    public function resultadoPublic(string $codigo_acceso, string $resultado): View
    {
        $encuesta = $this->obtenerEncuestaPorCodigo($codigo_acceso);
        $resultadoId = $this->decodeResultadoId($resultado);
        $resultado = EncuestaResultado::findOrFail($resultadoId);

        if ($resultado->encuesta_id !== $encuesta->id) {
            abort(404);
        }

        return $this->mostrarResultado($resultado);
    }

    /**
     * Mostrar detalles de respuestas
     */
    public function detalles(EncuestaResultado $resultado): View
    {
        return $this->mostrarDetalles($resultado);
    }

    public function detallesPublic(string $codigo_acceso, string $resultado): View
    {
        $encuesta = $this->obtenerEncuestaPorCodigo($codigo_acceso);
        $resultadoId = $this->decodeResultadoId($resultado);
        $resultado = EncuestaResultado::findOrFail($resultadoId);

        if ($resultado->encuesta_id !== $encuesta->id) {
            abort(404);
        }

        return $this->mostrarDetalles($resultado);
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

    private function mostrarFormularioInicio(Encuesta $encuesta): View
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('pages.encuestas.encuestas-iniciar', [
            'title' => 'Iniciar Encuesta',
            'encuesta' => $encuesta,
            'carreras' => $carreras,
            'formActionUrl' => route('encuestas.public.guardar-datos', ['codigo_acceso' => $encuesta->codigo_acceso]),
            'backUrl' => Auth::check()
                ? route('encuestas.index')
                : route('encuestas.public.iniciar', ['codigo_acceso' => $encuesta->codigo_acceso]),
        ]);
    }

    private function guardarDatosIniciales(Request $request, Encuesta $encuesta)
    {
        $validated = $request->validate([
            'nombre_estudiante' => 'required|string|max:255',
            'documento_estudiante' => [
                'required',
                'string',
                'regex:/^[A-Za-z]\d{8}$/',
                Rule::unique('encuestas_resultados', 'documento_estudiante')
                    ->where(fn($query) => $query->where('encuesta_id', $encuesta->id)),
            ],
            'edad_estudiante' => 'required|integer|min:5|max:100',
            'sexo_estudiante' => 'required|in:M,F,O',
            'carrera_id' => 'nullable|exists:carreras,id',
        ]);

        $request->merge([
            'documento_estudiante' => strtoupper($request->documento_estudiante),
        ]);

        $resultado = EncuestaResultado::create(array_merge($validated, [
            'encuesta_id' => $encuesta->id,
        ]));

        return redirect()->route('encuestas.public.responder', [
            'codigo_acceso' => $encuesta->codigo_acceso,
            'resultado' => $this->encodeResultadoId($resultado->id),
        ]);
    }

    private function mostrarFormularioRespuesta(EncuestaResultado $resultado): View
    {
        $encuesta = $resultado->encuesta;

        return view('pages.encuestas.encuestas-responder', [
            'title'            => 'Responder Encuesta',
            'encuesta'         => $encuesta,
            'resultado'        => $resultado,
            'resultadoUrl'     => isset($encuesta->codigo_acceso)
                ? route('encuestas.public.resultado', [
                    'codigo_acceso' => $encuesta->codigo_acceso,
                    'resultado'     => urlencode(base64_encode(encrypt($resultado->id)))
                ])
                : route('resultado', $resultado), // tu ruta privada
        ]);
    }

    private function mostrarResultado(EncuestaResultado $resultado): View
    {
        $analisis = $resultado->analisisTda;

        return view('pages.encuestas.encuestas-resultado', [
            'title' => 'Resultado de Encuesta',
            'resultado' => $resultado,
            'analisis' => $analisis
        ]);
    }

    private function mostrarDetalles(EncuestaResultado $resultado): View
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

    private function obtenerEncuestaPorCodigo(string $codigo_acceso): Encuesta
    {
        return Encuesta::where('codigo_acceso', $codigo_acceso)->firstOrFail();
    }

    private function encodeResultadoId(int $id): string
    {
        return base64_encode(encrypt($id));
    }

    private function decodeResultadoId(string $token): int
    {
        return (int) decrypt(base64_decode(urldecode($token)));
    }
}
