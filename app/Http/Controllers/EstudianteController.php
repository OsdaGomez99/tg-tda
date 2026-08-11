<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\EncuestaResultado;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstudianteController extends Controller
{
    /**
     * Listado de estudiantes únicos (agrupados por documento), a partir de
     * su participación más reciente en encuestas_resultados.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $carreraId = (string) $request->query('carrera_id', '');

        $ultimosIds = EncuestaResultado::selectRaw('MAX(id) as id')
            ->groupBy('documento_estudiante');

        $query = EncuestaResultado::with('carrera')
            ->whereIn('id', $ultimosIds);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_estudiante', 'like', "%{$search}%")
                    ->orWhere('documento_estudiante', 'like', "%{$search}%");
            });
        }

        if ($carreraId !== '') {
            $query->where('carrera_id', $carreraId);
        }

        $estudiantes = $query->orderBy('nombre_estudiante')
            ->paginate(15)
            ->withQueryString();

        $documentos = $estudiantes->pluck('documento_estudiante');

        $resumen = EncuestaResultado::whereIn('documento_estudiante', $documentos)
            ->selectRaw('documento_estudiante, COUNT(*) as total_encuestas, COUNT(DISTINCT semestre_id) as total_semestres')
            ->groupBy('documento_estudiante')
            ->get()
            ->keyBy('documento_estudiante');

        return view('pages.estudiantes.estudiantes-index', [
            'title' => 'Estudiantes',
            'estudiantes' => $estudiantes,
            'resumen' => $resumen,
            'carreras' => Carrera::orderBy('nombre')->get(),
            'search' => $search,
            'carreraId' => $carreraId,
        ]);
    }

    /**
     * Estadística de un estudiante: historial de participación por semestre
     * y datos para la gráfica de evolución de puntuaciones.
     */
    public function show(string $documento): View
    {
        $documento = strtoupper($documento);

        $resultados = EncuestaResultado::where('documento_estudiante', $documento)
            ->with(['semestre', 'analisisTda', 'encuesta', 'carrera'])
            ->get();

        if ($resultados->isEmpty()) {
            abort(404);
        }

        $resultados = $resultados->sortBy(fn($r) => $r->semestre?->id ?? $r->id)->values();

        $estudiante = $resultados->last();

        $chartData = [
            'labels' => $resultados->map(fn($r) => $r->semestre->nombre ?? 'Sin semestre')->all(),
            'inatencion' => $resultados->map(fn($r) => $r->analisisTda->puntuacion_inatencion ?? null)->all(),
            'hiperactividad' => $resultados->map(fn($r) => $r->analisisTda->puntuacion_hiperactividad ?? null)->all(),
            'total' => $resultados->map(fn($r) => $r->analisisTda->puntuacion_total ?? null)->all(),
        ];

        return view('pages.estudiantes.estudiantes-show', [
            'title' => 'Estadísticas del Estudiante',
            'documento' => $documento,
            'estudiante' => $estudiante,
            'resultados' => $resultados,
            'chartData' => $chartData,
            'totalSemestres' => $resultados->pluck('semestre_id')->unique()->count(),
        ]);
    }
}
