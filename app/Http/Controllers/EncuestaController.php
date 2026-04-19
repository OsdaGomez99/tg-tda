<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use Illuminate\Http\Request;

class EncuestaController extends Controller
{
    public function index()
    {
        $encuestas = Encuesta::with('usuario')->get();

        return view('pages.encuestas.encuestas-index', [
            'title' => 'Encuestas',
            'encuestas' => $encuestas
        ]);
    }

    public function create()
    {
        return view('pages.encuestas.encuestas-create', [
            'title' => 'Nueva Encuesta'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:encuestas',
            'usuario_id' => 'required|exists:users,id'
        ]);

        Encuesta::create($validated);

        return redirect()->route('encuestas')->with('success', 'Encuesta creada correctamente');
    }
}
