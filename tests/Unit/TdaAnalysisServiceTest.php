<?php

namespace Tests\Unit;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\Pregunta;
use App\Models\RespuestaEncuesta;
use App\Services\TdaAnalysisService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TdaAnalysisServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::defaultStringLength(191);
        Schema::dropIfExists('respuestas_encuestas');
        Schema::dropIfExists('encuesta_pregunta');
        Schema::dropIfExists('analisis_tda');
        Schema::dropIfExists('encuestas_resultados');
        Schema::dropIfExists('encuestas');
        Schema::dropIfExists('preguntas');

        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('tipo_tda')->nullable();
            $table->timestamps();
        });

        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('codigo_acceso')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
        });

        Schema::create('encuestas_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas');
            $table->string('nombre_estudiante')->nullable();
            $table->integer('edad_estudiante')->nullable();
            $table->string('sexo_estudiante')->nullable();
            $table->timestamps();
        });

        Schema::create('encuesta_pregunta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas')->cascadeOnDelete();
            $table->foreignId('pregunta_id')->constrained('preguntas')->cascadeOnDelete();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('respuestas_encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_resultado_id')->constrained('encuestas_resultados')->cascadeOnDelete();
            $table->foreignId('pregunta_id')->constrained('preguntas')->cascadeOnDelete();
            $table->integer('puntuacion');
            $table->timestamps();
        });

        Schema::create('analisis_tda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_resultado_id')->constrained('encuestas_resultados')->cascadeOnDelete();
            $table->integer('puntuacion_inatención')->default(0);
            $table->integer('puntuacion_hiperactividad')->default(0);
            $table->integer('puntuacion_total')->default(0);
            $table->integer('sintomas_inatención')->default(0);
            $table->integer('sintomas_hiperactividad')->default(0);
            $table->integer('umbral_sintomas')->default(6);
            $table->string('resultado')->nullable();
            $table->decimal('porcentaje_inatención', 5, 2)->default(0);
            $table->decimal('porcentaje_hiperactividad', 5, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function test_generar_analisis_uses_only_questions_assigned_to_the_survey(): void
    {
        $service = new TdaAnalysisService();

        $encuesta = Encuesta::create([
            'nombre' => 'Encuesta de prueba',
            'descripcion' => 'Prueba',
            'usuario_id' => 1,
        ]);

        $preguntaAsignada1 = Pregunta::create([
            'nombre' => 'Pregunta asignada 1',
            'descripcion' => 'Inatención',
            'estado' => true,
            'tipo_tda' => 'I',
        ]);

        $preguntaAsignada2 = Pregunta::create([
            'nombre' => 'Pregunta asignada 2',
            'descripcion' => 'Hiperactividad',
            'estado' => true,
            'tipo_tda' => 'H',
        ]);

        $preguntaNoAsignada = Pregunta::create([
            'nombre' => 'Pregunta no asignada',
            'descripcion' => 'Debería ignorarse',
            'estado' => true,
            'tipo_tda' => 'I',
        ]);

        $encuesta->preguntas()->attach([
            $preguntaAsignada1->id => ['orden' => 1],
            $preguntaAsignada2->id => ['orden' => 2],
        ]);

        $resultado = EncuestaResultado::create([
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

        $this->assertSame(3, $analisis->puntuacion_inatención);
        $this->assertSame(3, $analisis->puntuacion_hiperactividad);
        $this->assertSame('no_tda', $analisis->resultado);
    }

    /**
     * Crea una encuesta con 9 preguntas de inatención asignadas.
     */
    private function crearEncuestaInatencion(): Encuesta
    {
        $encuesta = Encuesta::create([
            'nombre' => 'Encuesta DSM-5',
            'descripcion' => 'Prueba de umbral por edad',
            'usuario_id' => 1,
        ]);

        for ($i = 1; $i <= 9; $i++) {
            $pregunta = Pregunta::create([
                'nombre' => "Síntoma de inatención {$i}",
                'descripcion' => 'Inatención',
                'estado' => true,
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

        $resultado = EncuestaResultado::create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Estudiante universitario',
            'edad_estudiante' => 17,
            'sexo_estudiante' => 'F',
        ]);

        $this->responder($resultado, $encuesta, 5);

        $analisis = $service->generarAnalisis($resultado);

        $this->assertSame(5, $analisis->sintomas_inatención);
        $this->assertSame(5, $analisis->umbral_sintomas);
        $this->assertSame('tda_inatento', $analisis->resultado);
    }

    public function test_umbral_de_seis_sintomas_se_mantiene_en_menores_de_17(): void
    {
        $service  = new TdaAnalysisService();
        $encuesta = $this->crearEncuestaInatencion();

        $resultado = EncuestaResultado::create([
            'encuesta_id' => $encuesta->id,
            'nombre_estudiante' => 'Estudiante menor',
            'edad_estudiante' => 16,
            'sexo_estudiante' => 'M',
        ]);

        // Misma cantidad de síntomas que el caso anterior
        $this->responder($resultado, $encuesta, 5);

        $analisis = $service->generarAnalisis($resultado);

        $this->assertSame(5, $analisis->sintomas_inatención);
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
