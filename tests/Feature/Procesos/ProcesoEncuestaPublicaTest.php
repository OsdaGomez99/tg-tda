<?php

namespace Tests\Feature\Procesos;

use App\Models\Carrera;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Pregunta;
use App\Models\Semestre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcesoEncuestaPublicaTest extends TestCase
{
    use RefreshDatabase;

    private function crearEncuestaConPreguntas(): Encuesta
    {
        $encuesta = Encuesta::factory()->create(['codigo_acceso' => 'ABCD']);

        foreach (range(1, 9) as $i) {
            $pregunta = Pregunta::factory()->inatencion()->create();
            $encuesta->preguntas()->attach($pregunta->id, ['orden' => $i]);
        }

        return $encuesta;
    }

    public function test_flujo_completo_de_respuesta_publica_por_codigo_de_acceso(): void
    {
        Semestre::factory()->activo()->create();
        $carrera = Carrera::factory()->create();
        $encuesta = $this->crearEncuestaConPreguntas();

        // 1. Formulario inicial
        $this->get(route('encuestas.public.iniciar', $encuesta->codigo_acceso))
            ->assertOk()
            ->assertViewIs('pages.encuestas.encuestas-iniciar');

        // 2. Guardar datos del estudiante
        $storeResponse = $this->post(route('encuestas.public.guardar-datos', $encuesta->codigo_acceso), [
            'nombre_estudiante' => 'Juan Pérez',
            'documento_estudiante' => 'v12345678',
            'edad_estudiante' => 20,
            'sexo_estudiante' => 'M',
            'carrera_id' => $carrera->id,
        ]);

        $storeResponse->assertRedirect();
        $resultado = EncuestaResultado::where('encuesta_id', $encuesta->id)->firstOrFail();
        $this->assertSame('V12345678', $resultado->documento_estudiante);

        // 3. Formulario de respuesta
        $this->get($storeResponse->headers->get('Location'))
            ->assertOk()
            ->assertViewIs('pages.encuestas.encuestas-responder');

        // 4. Consultar preguntas vía API con el código de acceso
        $apiShow = $this->getJson(route('api.encuestas.encuestas.show', $encuesta) . '?codigo_acceso=' . $encuesta->codigo_acceso);
        $apiShow->assertOk()->assertJson(['success' => true, 'total_preguntas' => 9]);

        // 5. Responder cada pregunta (5 con síntoma presente, umbral de 6 para menor de 17... aquí edad 20 => umbral 5)
        foreach ($encuesta->preguntas as $indice => $pregunta) {
            $puntuacion = $indice < 5 ? 3 : 0;

            $respuestaResponse = $this->postJson(route('respuestas.store', $resultado), [
                'pregunta_id' => $pregunta->id,
                'puntuacion' => $puntuacion,
                'codigo_acceso' => $encuesta->codigo_acceso,
            ]);

            $respuestaResponse->assertOk()->assertJson(['success' => true]);
        }

        $this->assertDatabaseCount('respuestas_encuestas', 9);

        // 6. Finalizar la encuesta y generar el análisis
        $finalizarResponse = $this->postJson(route('respuestas.finalizar', $resultado), [
            'codigo_acceso' => $encuesta->codigo_acceso,
        ]);

        $finalizarResponse->assertOk();
        $finalizarResponse->assertJsonPath('success', true);
        $finalizarResponse->assertJsonPath('analisis.resultado', 'tda_inatento');

        $this->assertDatabaseHas('analisis_tda', [
            'encuesta_resultado_id' => $resultado->id,
            'sintomas_inatencion' => 5,
            'resultado' => 'tda_inatento',
        ]);

        // 7. Ver el resultado
        $resultadoUrlToken = urlencode(base64_encode(encrypt($resultado->id)));
        $this->get(route('encuestas.public.resultado', [
            'codigo_acceso' => $encuesta->codigo_acceso,
            'resultado' => $resultadoUrlToken,
        ]))->assertOk()->assertViewIs('pages.encuestas.encuestas-resultado');

        // 8. Descargar el PDF del resultado
        $pdfResponse = $this->get(route('encuestas.public.resultado.pdf', [
            'codigo_acceso' => $encuesta->codigo_acceso,
            'resultado' => $resultadoUrlToken,
        ]));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');

        // 9. Ver el detalle de respuestas
        $this->get(route('encuestas.public.detalles', [
            'codigo_acceso' => $encuesta->codigo_acceso,
            'resultado' => $resultadoUrlToken,
        ]))->assertOk()->assertViewIs('pages.encuestas.encuestas-detalles');
    }

    public function test_no_permite_iniciar_datos_sin_semestre_activo(): void
    {
        $encuesta = $this->crearEncuestaConPreguntas();
        $carrera = Carrera::factory()->create();

        $response = $this->post(route('encuestas.public.guardar-datos', $encuesta->codigo_acceso), [
            'nombre_estudiante' => 'Juan Pérez',
            'documento_estudiante' => 'V12345678',
            'edad_estudiante' => 20,
            'sexo_estudiante' => 'M',
            'carrera_id' => $carrera->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('encuestas_resultados', 0);
    }

    public function test_rechaza_un_documento_de_estudiante_con_formato_invalido(): void
    {
        Semestre::factory()->activo()->create();
        $encuesta = $this->crearEncuestaConPreguntas();
        $carrera = Carrera::factory()->create();

        $response = $this->post(route('encuestas.public.guardar-datos', $encuesta->codigo_acceso), [
            'nombre_estudiante' => 'Juan Pérez',
            'documento_estudiante' => '12345678',
            'edad_estudiante' => 20,
            'sexo_estudiante' => 'M',
            'carrera_id' => $carrera->id,
        ]);

        $response->assertSessionHasErrors('documento_estudiante');
    }

    public function test_rechaza_un_documento_duplicado_en_la_misma_encuesta_y_semestre(): void
    {
        $semestre = Semestre::factory()->activo()->create();
        $encuesta = $this->crearEncuestaConPreguntas();
        $carrera = Carrera::factory()->create();

        EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'semestre_id' => $semestre->id,
            'documento_estudiante' => 'V12345678',
        ]);

        $response = $this->post(route('encuestas.public.guardar-datos', $encuesta->codigo_acceso), [
            'nombre_estudiante' => 'Otro Estudiante',
            'documento_estudiante' => 'V12345678',
            'edad_estudiante' => 20,
            'sexo_estudiante' => 'M',
            'carrera_id' => $carrera->id,
        ]);

        $response->assertSessionHasErrors('documento_estudiante');
        $this->assertDatabaseCount('encuestas_resultados', 1);
    }

    public function test_codigo_de_acceso_inexistente_devuelve_404(): void
    {
        $this->get(route('encuestas.public.iniciar', 'ZZZZ'))->assertNotFound();
    }

    public function test_la_api_rechaza_peticiones_sin_codigo_de_acceso_valido(): void
    {
        $encuesta = $this->crearEncuestaConPreguntas();

        $response = $this->getJson(route('api.encuestas.encuestas.show', $encuesta) . '?codigo_acceso=INVALIDO');

        $response->assertForbidden();
    }

    public function test_no_permite_guardar_una_respuesta_de_una_pregunta_ajena_a_la_encuesta(): void
    {
        Semestre::factory()->activo()->create();
        $encuesta = $this->crearEncuestaConPreguntas();
        $resultado = EncuestaResultado::factory()->create(['encuesta_id' => $encuesta->id]);
        $preguntaAjena = Pregunta::factory()->create();

        $response = $this->postJson(route('respuestas.store', $resultado), [
            'pregunta_id' => $preguntaAjena->id,
            'puntuacion' => 2,
            'codigo_acceso' => $encuesta->codigo_acceso,
        ]);

        $response->assertStatus(400)->assertJson(['success' => false]);
    }

    public function test_no_permite_finalizar_si_faltan_respuestas(): void
    {
        Semestre::factory()->activo()->create();
        $encuesta = $this->crearEncuestaConPreguntas();
        $resultado = EncuestaResultado::factory()->create(['encuesta_id' => $encuesta->id]);

        $response = $this->postJson(route('respuestas.finalizar', $resultado), [
            'codigo_acceso' => $encuesta->codigo_acceso,
        ]);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertDatabaseCount('analisis_tda', 0);
    }

    public function test_un_usuario_autenticado_con_permiso_puede_eliminar_un_resultado(): void
    {
        $this->actingAsAdmin();

        $encuesta = Encuesta::factory()->create();
        $resultado = EncuestaResultado::factory()->create(['encuesta_id' => $encuesta->id]);

        $response = $this->delete(route('resultados.destroy', $resultado));

        $response->assertRedirect(route('estadisticas-encuesta', $encuesta));
        $this->assertDatabaseMissing('encuestas_resultados', ['id' => $resultado->id]);
    }
}
