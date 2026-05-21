<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EncuestaController extends Controller
{
    /**
     * Mostrar lista de encuestas
     */
    public function index(): View
    {
        $encuestas = Encuesta::with(['usuario', 'preguntas'])->get();

        return view('pages.encuestas.encuestas-index', [
            'title' => 'Encuestas',
            'encuestas' => $encuestas
        ]);
    }

    /**
     * Mostrar formulario de crear encuesta
     */
    public function create(): View
    {
        $preguntasDisponibles = Pregunta::where('estado', true)
            ->whereNotNull('tipo_tda')
            ->orderBy('tipo_tda')
            ->orderBy('id')
            ->get();

        return view('pages.encuestas.encuestas-create', [
            'title' => 'Nueva Encuesta',
            'preguntasDisponibles' => $preguntasDisponibles
        ]);
    }

    /**
     * Guardar nueva encuesta y asignar preguntas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:encuestas,nombre',
            'usuario_id' => 'required|exists:users,id',
            'pregunta_ids' => 'required|array|min:1',
            'pregunta_ids.*' => 'integer|exists:preguntas,id',
        ]);

        // Crear encuesta
        $encuesta = Encuesta::create([
            'nombre' => $validated['nombre'],
            'usuario_id' => $validated['usuario_id'],
        ]);

        // Preparar datos para sincronización con orden
        $sync = [];
        foreach ($validated['pregunta_ids'] as $index => $preguntaId) {
            $sync[$preguntaId] = ['orden' => $index + 1];
        }

        // Asignar preguntas
        $encuesta->preguntas()->sync($sync);

        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta creada y preguntas asignadas correctamente.');
    }

    /**
     * Mostrar página para asignar preguntas a una encuesta
     */
    public function asignarPreguntasForm(Encuesta $encuesta): View
    {
        $preguntasDisponibles = Pregunta::where('estado', true)
            ->whereNotNull('tipo_tda')
            ->orderBy('tipo_tda')
            ->orderBy('id')
            ->get();

        $preguntasAsignadas = $encuesta->preguntas()->pluck('pregunta_id')->toArray();

        return view('pages.encuestas.encuestas-asignar-preguntas', [
            'title' => 'Asignar Preguntas',
            'encuesta' => $encuesta,
            'preguntasDisponibles' => $preguntasDisponibles,
            'preguntasAsignadas' => $preguntasAsignadas,
        ]);
    }

    /**
     * Guardar preguntas asignadas a una encuesta
     */
    public function asignarPreguntas(Request $request, Encuesta $encuesta)
    {
        $validated = $request->validate([
            'pregunta_ids' => 'required|array|min:1',
            'pregunta_ids.*' => 'integer|exists:preguntas,id',
        ]);

        // Preparar datos para sincronización con orden
        $sync = [];
        foreach ($validated['pregunta_ids'] as $index => $preguntaId) {
            $sync[$preguntaId] = ['orden' => $index + 1];
        }

        // Sincronizar preguntas con la encuesta
        $encuesta->preguntas()->sync($sync);

        return redirect()->route('encuestas')
            ->with('success', 'Preguntas asignadas correctamente a la encuesta.');
    }

    /**
     * Editar encuesta
     */
    public function edit(Encuesta $encuesta): View
    {
        return view('pages.encuestas.encuestas-edit', [
            'title' => 'Editar Encuesta',
            'encuesta' => $encuesta
        ]);
    }

    /**
     * Actualizar encuesta
     */
    public function update(Request $request, Encuesta $encuesta)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:encuestas,nombre,' . $encuesta->id,
        ]);

        $encuesta->update($validated);

        return redirect()->route('encuestas')
            ->with('success', 'Encuesta actualizada correctamente.');
    }

    /**
     * Eliminar encuesta
     */
    public function destroy(Encuesta $encuesta)
    {
        $encuesta->delete();

        return redirect()->route('encuestas')
            ->with('success', 'Encuesta eliminada correctamente.');
    }
}
