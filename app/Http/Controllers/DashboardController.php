<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Services\TdaAnalysisService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private TdaAnalysisService $tdaService) {}

    /**
     * Mostrar el panel general con estadísticas de todo lo evaluado (todas las encuestas)
     */
    public function index(): View
    {
        $resultados = EncuestaResultado::with(['analisisTda', 'encuesta'])->get();

        $estadisticas = $this->tdaService->calcularEstadisticas($resultados);

        $prioritarios = $resultados
            ->filter(fn (EncuestaResultado $resultado) => $resultado->analisisTda && $resultado->analisisTda->resultado !== 'no_tda')
            ->sortByDesc(fn (EncuestaResultado $resultado) => $resultado->analisisTda->puntuacion_total)
            ->take(10);

        return view('pages.dashboard.ecommerce', [
            'title' => 'Inicio',
            'totalEncuestas' => Encuesta::count(),
            'estadisticas' => $estadisticas,
            'prioritarios' => $prioritarios,
        ]);
    }
}
