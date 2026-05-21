<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index()
    {
        $preguntas = Pregunta::get();

        return view('pages.preguntas.preguntas-index', [
            'title' => 'Preguntas',
            'preguntas' => $preguntas
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
            'ejemplo' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        // Asegurar que estado sea booleano
        $validated['estado'] = $request->has('estado');

        Pregunta::create($validated);

        return redirect()->route('preguntas')->with('success', 'Pregunta creada correctamente');
    }

    public function show(Pregunta $pregunta)
    {
        return view('pages.preguntas.preguntas-show', [
            'title' => 'Detalle de Pregunta',
            'pregunta' => $pregunta
        ]);
    }

    public function edit(Pregunta $pregunta)
    {
        return view('pages.preguntas.preguntas-edit', [
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
            'ejemplo' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        // Asegurar que estado sea booleano
        $validated['estado'] = $request->has('estado');

        $pregunta->update($validated);

        return redirect()->route('preguntas')->with('success', 'Pregunta actualizada correctamente');
    }

    public function destroy(Pregunta $pregunta)
    {
        $pregunta->delete();

        return redirect()->route('preguntas')->with('success', 'Pregunta eliminada correctamente');
    }
}
