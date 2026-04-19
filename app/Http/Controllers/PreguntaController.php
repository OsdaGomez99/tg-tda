<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index()
    {
        $preguntas = Pregunta::with('categoria')->get();

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
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'estado' => 'boolean'
        ]);

        Pregunta::create($validated);

        return redirect()->route('preguntas')->with('success', 'Pregunta creada correctamente');
    }
}
