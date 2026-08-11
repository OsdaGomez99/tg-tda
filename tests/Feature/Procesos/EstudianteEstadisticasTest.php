<?php

namespace Tests\Feature\Procesos;

use App\Models\AnalisisTda;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Semestre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstudianteEstadisticasTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_el_historial_de_participacion_de_un_estudiante(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();
        $semestre1 = Semestre::factory()->create(['nombre' => '2025-1']);
        $semestre2 = Semestre::factory()->create(['nombre' => '2025-2']);

        $resultado1 = EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'documento_estudiante' => 'V99999999',
            'nombre_estudiante' => 'Estudiante Histórico',
            'semestre_id' => $semestre1->id,
        ]);
        AnalisisTda::create([
            'encuesta_resultado_id' => $resultado1->id,
            'puntuacion_inatencion' => 10,
            'puntuacion_hiperactividad' => 5,
            'puntuacion_total' => 15,
            'resultado' => 'tda_inatento',
        ]);

        $resultado2 = EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'documento_estudiante' => 'V99999999',
            'nombre_estudiante' => 'Estudiante Histórico',
            'semestre_id' => $semestre2->id,
        ]);
        AnalisisTda::create([
            'encuesta_resultado_id' => $resultado2->id,
            'puntuacion_inatencion' => 4,
            'puntuacion_hiperactividad' => 3,
            'puntuacion_total' => 7,
            'resultado' => 'no_tda',
        ]);

        $response = $this->get(route('estudiantes.show', 'v99999999'));

        $response->assertOk();
        $response->assertViewIs('pages.estudiantes.estudiantes-show');
        $response->assertViewHas('totalSemestres', 2);
        $response->assertViewHas('chartData', function (array $chartData) {
            return $chartData['labels'] === ['2025-1', '2025-2']
                && $chartData['total'] === [15, 7];
        });
    }

    public function test_normaliza_el_documento_a_mayusculas(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();
        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'documento_estudiante' => 'V88888888',
        ]);

        $this->get(route('estudiantes.show', 'v88888888'))->assertOk();
    }

    public function test_devuelve_404_para_un_estudiante_sin_resultados(): void
    {
        $this->actingAsAdmin();

        $this->get(route('estudiantes.show', 'V00000000'))->assertNotFound();
    }
}
