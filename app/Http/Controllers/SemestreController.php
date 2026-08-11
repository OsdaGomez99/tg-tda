<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemestreController extends Controller
{
    /**
     * Mostrar lista de semestres
     */
    public function index(): View
    {
        $semestres = Semestre::withCount('encuestaResultados')
            ->orderByDesc('nombre')
            ->paginate(10);

        return view('pages.semestres.semestres-index', [
            'title' => 'Semestres',
            'semestres' => $semestres,
        ]);
    }

    /**
     * Crear un nuevo semestre
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:20|unique:semestres,nombre',
        ]);

        Semestre::create($validated);

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre creado correctamente.');
    }

    /**
     * Marcar un semestre como el único activo
     */
    public function activar(Semestre $semestre)
    {
        $semestre->activar();

        return redirect()->route('semestres.index')
            ->with('success', "Semestre {$semestre->nombre} marcado como activo.");
    }

    /**
     * Eliminar un semestre sin respuestas asociadas
     */
    public function destroy(Semestre $semestre)
    {
        if ($semestre->encuestaResultados()->exists()) {
            return redirect()->route('semestres.index')
                ->with('error', 'No se puede eliminar un semestre con respuestas de encuesta asociadas.');
        }

        $semestre->delete();

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre eliminado correctamente.');
    }
}
