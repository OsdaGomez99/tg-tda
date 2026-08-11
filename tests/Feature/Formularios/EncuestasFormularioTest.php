<?php

namespace Tests\Feature\Formularios;

use App\Http\Controllers\EncuestaController;
use App\Models\Encuesta;
use App\Models\Pregunta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use ReflectionMethod;
use Tests\TestCase;

class EncuestasFormularioTest extends TestCase
{
    use RefreshDatabase;

    public function test_formulario_de_crear_encuesta_lista_solo_preguntas_activas_con_tipo_tda(): void
    {
        $this->actingAsAdmin();

        $activaConTipo = Pregunta::factory()->create(['estado' => true, 'tipo_tda' => 'I']);
        Pregunta::factory()->create(['estado' => false, 'tipo_tda' => 'I']);

        $response = $this->get(route('encuestas.create'));

        $response->assertOk();
        $response->assertViewHas(
            'preguntasDisponibles',
            fn ($preguntas) => $preguntas->pluck('id')->contains($activaConTipo->id) && $preguntas->count() === 1
        );
    }

    public function test_guarda_una_encuesta_y_asigna_preguntas_en_orden(): void
    {
        $admin = $this->actingAsAdmin();

        $pregunta1 = Pregunta::factory()->create();
        $pregunta2 = Pregunta::factory()->create();

        $response = $this->post(route('encuestas.store'), [
            'nombre' => 'Encuesta de diagnóstico',
            'descripcion' => 'Descripción de prueba',
            'usuario_id' => $admin->id,
            'pregunta_ids' => [$pregunta2->id, $pregunta1->id],
        ]);

        $response->assertRedirect(route('encuestas.index'));

        $encuesta = Encuesta::where('nombre', 'Encuesta de diagnóstico')->firstOrFail();

        $this->assertNotNull($encuesta->codigo_acceso);
        $this->assertSame(4, strlen($encuesta->codigo_acceso));
        $this->assertSame([$pregunta2->id, $pregunta1->id], $encuesta->preguntas->pluck('id')->all());
        $this->assertSame(1, $encuesta->preguntas->firstWhere('id', $pregunta2->id)->pivot->orden);
        $this->assertSame(2, $encuesta->preguntas->firstWhere('id', $pregunta1->id)->pivot->orden);
    }

    public function test_el_codigo_de_acceso_generado_excluye_caracteres_ambiguos(): void
    {
        $admin = $this->actingAsAdmin();
        $pregunta = Pregunta::factory()->create();

        foreach (range(1, 20) as $i) {
            $this->post(route('encuestas.store'), [
                'nombre' => "Encuesta {$i}",
                'usuario_id' => $admin->id,
                'pregunta_ids' => [$pregunta->id],
            ]);
        }

        $codigos = Encuesta::pluck('codigo_acceso')->implode('');

        foreach (['I', 'O', '1', '0'] as $caracter) {
            $this->assertStringNotContainsString($caracter, $codigos);
        }
    }

    public function test_el_codigo_de_acceso_generado_evita_colisionar_con_uno_existente(): void
    {
        Encuesta::factory()->create(['codigo_acceso' => 'ABCD']);

        // Fuerza que la primera generación aleatoria choque con el código ya
        // existente, y la segunda produzca uno libre. Se invoca el método
        // directamente (sin pasar por HTTP) porque el framework también usa
        // Str::random() internamente (p. ej. para el ID de sesión), lo que
        // consumiría la secuencia antes de llegar al controlador.
        Str::createRandomStringsUsingSequence(['ABCD', 'WXYZ']);

        try {
            $controller = new EncuestaController();
            $metodo = new ReflectionMethod($controller, 'generarCodigoAcceso');
            $metodo->setAccessible(true);
            $codigo = $metodo->invoke($controller);
        } finally {
            Str::createRandomStringsNormally();
        }

        $this->assertSame('WXYZ', $codigo);
    }

    public function test_no_guarda_una_encuesta_sin_preguntas(): void
    {
        $admin = $this->actingAsAdmin();

        $response = $this->post(route('encuestas.store'), [
            'nombre' => 'Encuesta sin preguntas',
            'usuario_id' => $admin->id,
            'pregunta_ids' => [],
        ]);

        $response->assertSessionHasErrors('pregunta_ids');
        $this->assertDatabaseMissing('encuestas', ['nombre' => 'Encuesta sin preguntas']);
    }

    public function test_no_guarda_una_encuesta_con_nombre_duplicado(): void
    {
        $admin = $this->actingAsAdmin();
        $pregunta = Pregunta::factory()->create();
        Encuesta::factory()->create(['nombre' => 'Encuesta repetida']);

        $response = $this->post(route('encuestas.store'), [
            'nombre' => 'Encuesta repetida',
            'usuario_id' => $admin->id,
            'pregunta_ids' => [$pregunta->id],
        ]);

        $response->assertSessionHasErrors('nombre');
        $this->assertDatabaseCount('encuestas', 1);
    }

    public function test_guarda_una_encuesta_sin_enviar_el_campo_descripcion(): void
    {
        $admin = $this->actingAsAdmin();
        $pregunta = Pregunta::factory()->create();

        $response = $this->post(route('encuestas.store'), [
            'nombre' => 'Encuesta sin descripción',
            'usuario_id' => $admin->id,
            'pregunta_ids' => [$pregunta->id],
        ]);

        $response->assertRedirect(route('encuestas.index'));
        $this->assertDatabaseHas('encuestas', [
            'nombre' => 'Encuesta sin descripción',
            'descripcion' => null,
        ]);
    }

    public function test_actualiza_una_encuesta_y_resincroniza_sus_preguntas(): void
    {
        $admin = $this->actingAsAdmin();

        $preguntaOriginal = Pregunta::factory()->create();
        $preguntaNueva = Pregunta::factory()->create();

        $encuesta = Encuesta::factory()->create(['usuario_id' => $admin->id]);
        $encuesta->preguntas()->attach($preguntaOriginal->id, ['orden' => 1]);

        $response = $this->put(route('encuestas.update', $encuesta), [
            'nombre' => 'Encuesta actualizada',
            'descripcion' => 'Descripción actualizada',
            'usuario_id' => $admin->id,
            'pregunta_ids' => [$preguntaNueva->id],
        ]);

        $response->assertRedirect(route('encuestas.index'));

        $encuesta->refresh();
        $this->assertSame('Encuesta actualizada', $encuesta->nombre);
        $this->assertSame([$preguntaNueva->id], $encuesta->preguntas->pluck('id')->all());
    }

    public function test_actualiza_una_encuesta_sin_enviar_el_campo_descripcion(): void
    {
        $admin = $this->actingAsAdmin();
        $pregunta = Pregunta::factory()->create();
        $encuesta = Encuesta::factory()->create(['usuario_id' => $admin->id, 'descripcion' => 'Original']);
        $encuesta->preguntas()->attach($pregunta->id, ['orden' => 1]);

        $response = $this->put(route('encuestas.update', $encuesta), [
            'nombre' => 'Encuesta actualizada sin descripción',
            'usuario_id' => $admin->id,
            'pregunta_ids' => [$pregunta->id],
        ]);

        $response->assertRedirect(route('encuestas.index'));
        $this->assertDatabaseHas('encuestas', [
            'id' => $encuesta->id,
            'nombre' => 'Encuesta actualizada sin descripción',
            'descripcion' => null,
        ]);
    }

    public function test_elimina_una_encuesta(): void
    {
        $admin = $this->actingAsAdmin();
        $encuesta = Encuesta::factory()->create(['usuario_id' => $admin->id]);

        $response = $this->delete(route('encuestas.destroy', $encuesta));

        $response->assertRedirect();
        $this->assertDatabaseMissing('encuestas', ['id' => $encuesta->id]);
    }
}
