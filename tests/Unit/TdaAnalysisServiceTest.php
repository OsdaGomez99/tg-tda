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
}
