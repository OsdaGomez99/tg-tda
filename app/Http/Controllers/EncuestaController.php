<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class EncuestaController extends Controller
{
    /**
     * Mostrar lista de encuestas
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $encuestas = Encuesta::with(['usuario', 'preguntas'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                        ->orWhere('nombre', 'like', "%{$search}%")
                        ->orWhereHas('usuario', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('pages.encuestas.encuestas-index', [
            'title' => 'Encuestas',
            'encuestas' => $encuestas,
            'search' => $search,
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
            'descripcion' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
            'pregunta_ids' => 'required|array|min:1',
            'pregunta_ids.*' => 'integer|exists:preguntas,id',
        ]);

        // Crear encuesta
        $encuesta = Encuesta::create([
            'nombre' => $validated['nombre'],
            'codigo_acceso' => $this->generarCodigoAcceso(),
            'descripcion' => $validated['descripcion'] ?? null,
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
     * Editar encuesta
     */
    public function show(Encuesta $encuesta): View
    {
        $preguntasDisponibles = Pregunta::where('estado', true)
            ->whereNotNull('tipo_tda')
            ->orderBy('tipo_tda')
            ->orderBy('id')
            ->get();

        return view('pages.encuestas.encuestas-show', [
            'title' => 'Editar Encuesta',
            'encuesta' => $encuesta,
            'preguntasDisponibles' => $preguntasDisponibles
        ]);
    }

    /**
     * Actualizar encuesta
     */
    public function update(Request $request, Encuesta $encuesta)
    {
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'usuario_id'    => 'required|exists:users,id',
            'pregunta_ids'  => 'required|array|min:1',
            'pregunta_ids.*' => 'integer|exists:preguntas,id',
        ]);

        // Solo actualizar campos de la encuesta, no pregunta_ids
        $encuesta->update([
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'usuario_id'  => $validated['usuario_id'],
        ]);

        $sync = [];
        foreach ($validated['pregunta_ids'] as $index => $preguntaId) {
            $sync[$preguntaId] = ['orden' => $index + 1];
        }

        $encuesta->preguntas()->sync($sync);

        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta actualizada correctamente.');
    }

    /**
     * Eliminar encuesta
     */
    public function destroy(Encuesta $encuesta)
    {
        $encuesta->delete();

        return redirect()->route('encuestas.index', $encuesta)
            ->with('success', 'Encuesta eliminada correctamente.');
    }


    /**
     * Genera un código aleatorio para una encuesta.
     * Este método genera un código aleatorio de una longitud especificada, evitando caracteres excluidos.
     * @return string El código generado.
     */
    protected function generarCodigoAcceso()
    {
        $longitud = 4; // Longitud del código de acceso
        $codigo = '';
        do {
            $codigo = Str::upper(Str::random($longitud));
        } while ($this->caracteresExcluidos($codigo) || Encuesta::where('codigo_acceso', $codigo)->exists());

        return $codigo;
    }

    /**
     * Verifica si un código contiene caracteres excluidos.
     * Este método busca si un código dado contiene alguno de los caracteres especificados en un arreglo.
     * @param string $codigo El código a verificar.
     * @return bool `true` si el código contiene al menos un carácter excluido, `false` en caso contrario.
     */
    protected function caracteresExcluidos(string $codigo): bool
    {
        $caracteres_excluidos = ['I', 'O', '1', '0']; // Caracteres a excluir
        foreach ($caracteres_excluidos as $caracter) {
            if (Str::contains($codigo, $caracter)) {
                return true;
            }
        }
        return false;
    }
}
