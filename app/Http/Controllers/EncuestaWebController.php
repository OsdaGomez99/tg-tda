<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Semestre;
use App\Services\TdaAnalysisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class EncuestaWebController extends Controller
{
    public function __construct(private TdaAnalysisService $tdaService) {}

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
     * Mostrar formulario de respuestas (usuario autenticado)
     */
    public function responder(EncuestaResultado $resultado): View
    {
        return $this->mostrarFormularioRespuesta($resultado);
    }

    /**
     * Mostrar resultado y análisis
     */
    public function resultado(EncuestaResultado $resultado): View
    {
        return $this->mostrarResultado($resultado);
    }

    /**
     * Mostrar resultado y análisis (acceso público por código)
     */
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
     * Descargar el resultado y análisis en PDF
     */
    public function resultadoPdf(EncuestaResultado $resultado): Response
    {
        return $this->generarPdfResultado($resultado);
    }

    /**
     * Descargar el resultado y análisis en PDF (acceso público por código)
     */
    public function resultadoPdfPublic(string $codigo_acceso, string $resultado): Response
    {
        $encuesta = $this->obtenerEncuestaPorCodigo($codigo_acceso);
        $resultadoId = $this->decodeResultadoId($resultado);
        $resultado = EncuestaResultado::findOrFail($resultadoId);

        if ($resultado->encuesta_id !== $encuesta->id) {
            abort(404);
        }

        return $this->generarPdfResultado($resultado);
    }

    /**
     * Mostrar detalles de respuestas
     */
    public function detalles(EncuestaResultado $resultado): View
    {
        return $this->mostrarDetalles($resultado);
    }

    /**
     * Mostrar detalles de respuestas (acceso público por código)
     */
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
    public function estadisticas(Encuesta $encuesta, Request $request): View
    {
        // Estadísticas agregadas: siempre sobre el total de la encuesta, sin filtrar
        $todosLosResultados = $encuesta->resultados()
            ->with('analisisTda')
            ->get();

        // Delegar cálculo al servicio, igual que ApiEncuestaController
        $estadisticas = $this->tdaService->calcularEstadisticas($todosLosResultados);

        // Listado paginado y filtrable de respondientes
        $search = trim((string) $request->query('search', ''));
        $resultadoFiltro = (string) $request->query('resultado', '');

        $query = $encuesta->resultados()->with('analisisTda');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_estudiante', 'like', "%{$search}%")
                    ->orWhere('documento_estudiante', 'like', "%{$search}%");
            });
        }

        if ($resultadoFiltro === 'pendiente') {
            $query->whereDoesntHave('analisisTda');
        } elseif ($resultadoFiltro !== '') {
            $query->whereHas('analisisTda', fn($q) => $q->where('resultado', $resultadoFiltro));
        }

        $resultados = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('pages.encuestas.encuestas-estadisticas', [
            'title'       => 'Estadísticas de Encuesta',
            'encuesta'    => $encuesta,
            'resultados'  => $resultados,
            'estadisticas' => $estadisticas,
            'search' => $search,
            'resultadoFiltro' => $resultadoFiltro,
        ]);
    }

    /**
     * Descargar las estadísticas consolidadas de una encuesta en PDF
     */
    public function estadisticasPdf(Encuesta $encuesta): Response|RedirectResponse
    {
        $resultados = $encuesta->resultados()
            ->with(['analisisTda', 'respuestas'])
            ->get();

        if ($resultados->isEmpty()) {
            return redirect()->route('estadisticas-encuesta', $encuesta)
                ->with('warning', 'No hay estadísticas disponibles para esta encuesta aún.');
        }

        $estadisticas = $this->tdaService->calcularEstadisticas($resultados);

        $pdf = Pdf::loadView('pages.encuestas.pdf.estadisticas-pdf', [
            'encuesta' => $encuesta,
            'resultados' => $resultados,
            'estadisticas' => $estadisticas,
        ]);

        $nombreArchivo = 'estadisticas-' . Str::slug($encuesta->nombre) . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Construir la vista del formulario inicial con la lista de carreras y las URLs de acción/retorno
     */
    private function mostrarFormularioInicio(Encuesta $encuesta): View
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('pages.encuestas.encuestas-iniciar', [
            'title' => 'Iniciar Encuesta',
            'encuesta' => $encuesta,
            'carreras' => $carreras,
            'semestre' => Semestre::actual(),
            'formActionUrl' => route('encuestas.public.guardar-datos', ['codigo_acceso' => $encuesta->codigo_acceso]),
            'backUrl' => Auth::check()
                ? route('encuestas.index')
                : route('encuestas.public.iniciar', ['codigo_acceso' => $encuesta->codigo_acceso]),
        ]);
    }

    /**
     * Validar y crear el resultado inicial del estudiante, luego redirigir a responder
     */
    private function guardarDatosIniciales(Request $request, Encuesta $encuesta)
    {
        $semestre = Semestre::actual();

        if (!$semestre) {
            return back()->withInput()
                ->with('error', 'No hay un semestre activo configurado. Contacte al administrador antes de responder esta encuesta.');
        }

        $validated = $request->validate([
            'nombre_estudiante' => 'required|string|max:255',
            'documento_estudiante' => [
                'required',
                'string',
                'regex:/^[A-Za-z]\d{8}$/',
                Rule::unique('encuestas_resultados', 'documento_estudiante')
                    ->where(fn($query) => $query->where('encuesta_id', $encuesta->id)
                        ->where('semestre_id', $semestre->id)),
            ],
            'edad_estudiante' => 'required|integer|min:15|max:100',
            'sexo_estudiante' => 'required|in:M,F,O',
            'carrera_id' => 'required|exists:carreras,id',
        ]);

        $request->merge([
            'documento_estudiante' => strtoupper($request->documento_estudiante),
        ]);

        $resultado = EncuestaResultado::create(array_merge($validated, [
            'encuesta_id' => $encuesta->id,
            'semestre_id' => $semestre->id,
        ]));

        return redirect()->route('encuestas.public.responder', [
            'codigo_acceso' => $encuesta->codigo_acceso,
            'resultado' => $this->encodeResultadoId($resultado->id),
        ]);
    }

    /**
     * Construir la vista para responder la encuesta, generando la URL de resultado (pública o privada)
     */
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
                : route('resultado-encuesta', $resultado),
        ]);
    }

    /**
     * Construir la vista de resultado con el análisis TDA asociado
     */
    private function mostrarResultado(EncuestaResultado $resultado): View
    {
        $analisis = $resultado->analisisTda;

        return view('pages.encuestas.encuestas-resultado', [
            'title' => 'Resultado de Encuesta',
            'resultado' => $resultado,
            'analisis' => $analisis
        ]);
    }

    /**
     * Generar el PDF de resultado y análisis para un estudiante
     */
    private function generarPdfResultado(EncuestaResultado $resultado): Response
    {
        $analisis = $resultado->analisisTda;

        if (!$analisis) {
            abort(404, 'El análisis de esta encuesta aún no está disponible.');
        }

        $badgeColors = [
            'tda_combinado' => '#dc2626',
            'tda_inatento' => '#d97706',
            'tda_hiperactivo' => '#ea580c',
            'tda_posible' => '#ca8a04',
            'no_tda' => '#16a34a',
        ];

        $resultadoLabels = [
            'tda_combinado' => 'TDA Combinado',
            'tda_inatento' => 'TDA Inatento',
            'tda_hiperactivo' => 'TDA Hiperactivo',
            'tda_posible' => 'Posible TDA',
            'no_tda' => 'Sin TDA',
        ];

        $pdf = Pdf::loadView('pages.encuestas.pdf.resultado-pdf', [
            'resultado' => $resultado,
            'analisis' => $analisis,
            'recomendaciones' => $analisis->getRecomendaciones(),
            'badgeColor' => $badgeColors[$analisis->resultado] ?? '#6b7280',
            'resultadoLabel' => $resultadoLabels[$analisis->resultado] ?? 'Desconocido',
        ]);

        $nombreArchivo = 'resultado-' . Str::slug($resultado->nombre_estudiante) . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Construir la vista de detalles con las respuestas (y sus preguntas) y el análisis TDA
     */
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

    /**
     * Eliminar el resultado de un estudiante, haya finalizado la encuesta o no.
     * Las respuestas y el análisis TDA asociados se eliminan en cascada a nivel de base de datos.
     */
    public function eliminarResultado(EncuestaResultado $resultado): RedirectResponse
    {
        $encuestaId = $resultado->encuesta_id;
        $nombreEstudiante = $resultado->nombre_estudiante;

        $resultado->delete();

        return redirect()->route('estadisticas-encuesta', $encuestaId)
            ->with('success', "Resultado de {$nombreEstudiante} eliminado correctamente.");
    }

    /**
     * Buscar la encuesta asociada a un código de acceso público (404 si no existe)
     */
    private function obtenerEncuestaPorCodigo(string $codigo_acceso): Encuesta
    {
        return Encuesta::where('codigo_acceso', $codigo_acceso)->firstOrFail();
    }

    /**
     * Cifrar y codificar en base64 el ID de un resultado para usarlo en URLs públicas
     */
    private function encodeResultadoId(int $id): string
    {
        return base64_encode(encrypt($id));
    }

    /**
     * Revertir encodeResultadoId: decodificar y descifrar el token para obtener el ID del resultado
     */
    private function decodeResultadoId(string $token): int
    {
        return (int) decrypt(base64_decode(urldecode($token)));
    }
}
