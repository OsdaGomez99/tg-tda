<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $preguntas = Pregunta::when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                        ->orWhere('nombre', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.preguntas.preguntas-index', [
            'title' => 'Preguntas',
            'preguntas' => $preguntas,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('pages.preguntas.preguntas-create', [
            'title' => 'Nueva Pregunta'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_tda' => 'required|in:I,H',
            'estado' => 'boolean'
        ]);

        // Asegurar que estado sea booleano
        $validated['estado'] = $request->has('estado');

        Pregunta::create($validated);

        return redirect()->route('preguntas.index')->with('success', 'Pregunta creada correctamente');
    }

    public function show(Pregunta $pregunta)
    {
        return view('pages.preguntas.preguntas-show', [
            'title' => 'Editar Pregunta',
            'pregunta' => $pregunta
        ]);
    }

    public function update(Request $request, Pregunta $pregunta)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo_tda' => 'required|in:I,H',
            'estado' => 'boolean'
        ]);

        // Asegurar que estado sea booleano
        $validated['estado'] = $request->has('estado');

        $pregunta->update($validated);

        return redirect()->route('preguntas.index')->with('success', 'Pregunta actualizada correctamente');
    }

    public function destroy(Pregunta $pregunta)
    {
        $pregunta->delete();

        return redirect()->route('preguntas.index')->with('success', 'Pregunta eliminada correctamente');
    }
}
