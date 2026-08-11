<?php

namespace Tests\Unit;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Pregunta;
use App\Models\RespuestaEncuesta;
use App\Services\TdaAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TdaAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generar_analisis_usa_unicamente_las_preguntas_asignadas_a_la_encuesta(): void
    {
        $service = new TdaAnalysisService();

        $encuesta = Encuesta::factory()->create([
            'nombre' => 'Encuesta de prueba',
            'descripcion' => 'Prueba',
        ]);

        $preguntaAsignada1 = Pregunta::factory()->create([
            'nombre' => 'Pregunta asignada 1',
            'descripcion' => 'Inatención',
            'tipo_tda' => 'I',
        ]);

        $preguntaAsignada2 = Pregunta::factory()->create([
            'nombre' => 'Pregunta asignada 2',
            'descripcion' => 'Hiperactividad',
            'tipo_tda' => 'H',
        ]);

        $preguntaNoAsignada = Pregunta::factory()->create([
            'nombre' => 'Pregunta no asignada',
            'descripcion' => 'Debería ignorarse',
            'tipo_tda' => 'I',
        ]);

        $encuesta->preguntas()->attach([
            $preguntaAsignada1->id => ['orden' => 1],
            $preguntaAsignada2->id => ['orden' => 2],
        ]);

        $resultado = EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Ana',
            'edad_estudiante' => 16,
            'sexo_estudiante' => 'F',
        ]);

        RespuestaEncuesta::create([
            'encuesta_resultado_id' => $resultado->id,
            'pregunta_id' => $preguntaAsignada1->id,
            'puntuacion' => 3,
        ]);

        RespuestaEncuesta::create([
            'encuesta_resultado_id' => $resultado->id,
            'pregunta_id' => $preguntaAsignada2->id,
            'puntuacion' => 3,
        ]);

        RespuestaEncuesta::create([
            'encuesta_resultado_id' => $resultado->id,
            'pregunta_id' => $preguntaNoAsignada->id,
            'puntuacion' => 3,
        ]);

        $analisis = $service->generarAnalisis($resultado);

        $this->assertSame(3, $analisis->puntuacion_inatencion);
        $this->assertSame(3, $analisis->puntuacion_hiperactividad);
        $this->assertSame('no_tda', $analisis->resultado);
    }

    /**
     * Crea una encuesta con 9 preguntas de inatención asignadas.
     */
    private function crearEncuestaInatencion(): Encuesta
    {
        $encuesta = Encuesta::factory()->create([
            'nombre' => 'Encuesta DSM-5',
            'descripcion' => 'Prueba de umbral por edad',
        ]);

        for ($i = 1; $i <= 9; $i++) {
            $pregunta = Pregunta::factory()->create([
                'nombre' => "Síntoma de inatención {$i}",
                'descripcion' => 'Inatención',
                'tipo_tda' => 'I',
            ]);

            $encuesta->preguntas()->attach([$pregunta->id => ['orden' => $i]]);
        }

        return $encuesta;
    }

    /**
     * Registra respuestas: las primeras $sintomas preguntas con puntuación 3
     * (síntoma presente) y el resto con 0.
     */
    private function responder(EncuestaResultado $resultado, Encuesta $encuesta, int $sintomas): void
    {
        foreach ($encuesta->preguntas as $indice => $pregunta) {
            RespuestaEncuesta::create([
                'encuesta_resultado_id' => $resultado->id,
                'pregunta_id' => $pregunta->id,
                'puntuacion' => $indice < $sintomas ? 3 : 0,
            ]);
        }
    }

    public function test_umbral_de_cinco_sintomas_se_aplica_desde_los_17_anios(): void
    {
        $service  = new TdaAnalysisService();
        $encuesta = $this->crearEncuestaInatencion();

        $resultado = EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Estudiante universitario',
            'edad_estudiante' => 17,
            'sexo_estudiante' => 'F',
        ]);

        $this->responder($resultado, $encuesta, 5);

        $analisis = $service->generarAnalisis($resultado);

        $this->assertSame(5, $analisis->sintomas_inatencion);
        $this->assertSame(5, $analisis->umbral_sintomas);
        $this->assertSame('tda_inatento', $analisis->resultado);
    }

    public function test_umbral_de_seis_sintomas_se_mantiene_en_menores_de_17(): void
    {
        $service  = new TdaAnalysisService();
        $encuesta = $this->crearEncuestaInatencion();

        $resultado = EncuestaResultado::factory()->create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Estudiante menor',
            'edad_estudiante' => 16,
            'sexo_estudiante' => 'M',
        ]);

        // Misma cantidad de síntomas que el caso anterior
        $this->responder($resultado, $encuesta, 5);

        $analisis = $service->generarAnalisis($resultado);

        $this->assertSame(5, $analisis->sintomas_inatencion);
        $this->assertSame(6, $analisis->umbral_sintomas);
        $this->assertSame('tda_posible', $analisis->resultado);
    }

    public function test_edad_desconocida_aplica_el_umbral_conservador(): void
    {
        $service = new TdaAnalysisService();

        $this->assertSame(6, $service->umbralSintomas(null));
        $this->assertSame(6, $service->umbralSintomas(16));
        $this->assertSame(5, $service->umbralSintomas(17));
        $this->assertSame(5, $service->umbralSintomas(30));
    }
}
