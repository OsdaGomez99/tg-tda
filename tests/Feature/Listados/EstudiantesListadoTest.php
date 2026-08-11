<?php

namespace Tests\Feature\Listados;

use App\Models\Carrera;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Semestre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstudiantesListadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_estudiantes_unicos_por_documento(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();

        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'documento_estudiante' => 'V12345678',
            'nombre_estudiante' => 'Estudiante Repetido',
            'semestre_id' => Semestre::factory()->create(['nombre' => '2025-1']),
        ]);
        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'documento_estudiante' => 'V12345678',
            'nombre_estudiante' => 'Estudiante Repetido',
            'semestre_id' => Semestre::factory()->create(['nombre' => '2025-2']),
        ]);

        $response = $this->get(route('estudiantes.index'));

        $response->assertOk();
        $response->assertViewHas('estudiantes', fn ($estudiantes) => $estudiantes->total() === 1);
        $response->assertViewHas(
            'resumen',
            fn ($resumen) => $resumen->get('V12345678')->total_encuestas === 2
                && $resumen->get('V12345678')->total_semestres === 2
        );
    }

    public function test_filtra_estudiantes_por_nombre_o_documento(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();

        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'María González',
            'documento_estudiante' => 'V11111111',
        ]);
        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Carlos Pérez',
            'documento_estudiante' => 'V22222222',
        ]);

        $response = $this->get(route('estudiantes.index', ['search' => 'González']));

        $response->assertSee('María González');
        $response->assertDontSee('Carlos Pérez');
    }

    public function test_filtra_estudiantes_por_carrera(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();
        $carreraA = Carrera::factory()->create();
        $carreraB = Carrera::factory()->create();

        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Estudiante Carrera A',
            'carrera_id' => $carreraA->id,
        ]);
        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Estudiante Carrera B',
            'carrera_id' => $carreraB->id,
        ]);

        $response = $this->get(route('estudiantes.index', ['carrera_id' => $carreraA->id]));

        $response->assertSee('Estudiante Carrera A');
        $response->assertDontSee('Estudiante Carrera B');
    }
}
