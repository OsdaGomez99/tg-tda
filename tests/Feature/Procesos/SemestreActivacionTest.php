<?php

namespace Tests\Feature\Procesos;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Semestre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemestreActivacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_activar_un_semestre_lo_marca_como_unico_activo(): void
    {
        $this->actingAsAdmin();

        $semestreActivo = Semestre::factory()->activo()->create(['nombre' => '2025-2']);
        $semestreNuevo = Semestre::factory()->create(['nombre' => '2026-1']);

        $response = $this->post(route('semestres.activar', $semestreNuevo));

        $response->assertRedirect(route('semestres.index'));

        $this->assertTrue($semestreNuevo->fresh()->activo);
        $this->assertFalse($semestreActivo->fresh()->activo);
    }

    public function test_semestre_actual_devuelve_el_unico_semestre_activo(): void
    {
        Semestre::factory()->create(['nombre' => '2025-2', 'activo' => false]);
        $activo = Semestre::factory()->activo()->create(['nombre' => '2026-1']);

        $this->assertTrue(Semestre::actual()->is($activo));
    }

    public function test_elimina_un_semestre_sin_respuestas_asociadas(): void
    {
        $this->actingAsAdmin();

        $semestre = Semestre::factory()->create();

        $response = $this->delete(route('semestres.destroy', $semestre));

        $response->assertRedirect(route('semestres.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('semestres', ['id' => $semestre->id]);
    }

    public function test_no_elimina_un_semestre_con_respuestas_asociadas(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();
        $semestre = Semestre::factory()->create();
        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'semestre_id' => $semestre->id,
        ]);

        $response = $this->delete(route('semestres.destroy', $semestre));

        $response->assertRedirect(route('semestres.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('semestres', ['id' => $semestre->id]);
    }
}
