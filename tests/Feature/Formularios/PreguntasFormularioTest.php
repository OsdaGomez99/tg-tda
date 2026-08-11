<?php

namespace Tests\Feature\Formularios;

use App\Models\Pregunta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreguntasFormularioTest extends TestCase
{
    use RefreshDatabase;

    public function test_formulario_de_crear_pregunta_es_accesible(): void
    {
        $this->actingAsAdmin();

        $this->get(route('preguntas.create'))
            ->assertOk()
            ->assertViewIs('pages.preguntas.preguntas-create');
    }

    public function test_guarda_una_pregunta_valida(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('preguntas.store'), [
            'nombre' => 'Con frecuencia olvida sus tareas diarias',
            'descripcion' => 'Ejemplo de descripción',
            'tipo_tda' => 'I',
            'estado' => '1',
        ]);

        $response->assertRedirect(route('preguntas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('preguntas', [
            'nombre' => 'Con frecuencia olvida sus tareas diarias',
            'tipo_tda' => 'I',
            'estado' => 1,
        ]);
    }

    public function test_el_estado_se_guarda_como_falso_cuando_no_se_envia_el_checkbox(): void
    {
        $this->actingAsAdmin();

        $this->post(route('preguntas.store'), [
            'nombre' => 'Pregunta sin marcar como activa',
            'tipo_tda' => 'H',
        ]);

        $this->assertDatabaseHas('preguntas', [
            'nombre' => 'Pregunta sin marcar como activa',
            'estado' => 0,
        ]);
    }

    public function test_el_codigo_se_autogenera_al_crear(): void
    {
        $this->actingAsAdmin();

        $this->post(route('preguntas.store'), [
            'nombre' => 'Pregunta con código autogenerado',
            'tipo_tda' => 'I',
        ]);

        $pregunta = Pregunta::where('nombre', 'Pregunta con código autogenerado')->firstOrFail();

        $this->assertSame('P-' . str_pad((string) $pregunta->id, 4, '0', STR_PAD_LEFT), $pregunta->codigo);
    }

    public function test_no_guarda_una_pregunta_sin_nombre(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('preguntas.store'), [
            'tipo_tda' => 'I',
        ]);

        $response->assertSessionHasErrors('nombre');
        $this->assertDatabaseCount('preguntas', 0);
    }

    public function test_no_guarda_una_pregunta_con_tipo_tda_invalido(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('preguntas.store'), [
            'nombre' => 'Pregunta con tipo inválido',
            'tipo_tda' => 'X',
        ]);

        $response->assertSessionHasErrors('tipo_tda');
        $this->assertDatabaseCount('preguntas', 0);
    }

    public function test_formulario_de_editar_pregunta_muestra_los_datos_actuales(): void
    {
        $this->actingAsAdmin();

        $pregunta = Pregunta::factory()->create(['nombre' => 'Pregunta existente']);

        $this->get(route('preguntas.show', $pregunta))
            ->assertOk()
            ->assertViewHas('pregunta', fn (Pregunta $p) => $p->is($pregunta));
    }

    public function test_actualiza_una_pregunta_existente(): void
    {
        $this->actingAsAdmin();

        $pregunta = Pregunta::factory()->create([
            'nombre' => 'Nombre original',
            'tipo_tda' => 'I',
            'estado' => true,
        ]);

        $response = $this->put(route('preguntas.update', $pregunta), [
            'nombre' => 'Nombre actualizado',
            'tipo_tda' => 'H',
        ]);

        $response->assertRedirect(route('preguntas.index'));

        $this->assertDatabaseHas('preguntas', [
            'id' => $pregunta->id,
            'nombre' => 'Nombre actualizado',
            'tipo_tda' => 'H',
            'estado' => 0,
        ]);
    }

    public function test_elimina_una_pregunta(): void
    {
        $this->actingAsAdmin();

        $pregunta = Pregunta::factory()->create();

        $response = $this->delete(route('preguntas.destroy', $pregunta));

        $response->assertRedirect(route('preguntas.index'));
        $this->assertDatabaseMissing('preguntas', ['id' => $pregunta->id]);
    }
}
