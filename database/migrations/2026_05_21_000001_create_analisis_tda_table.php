<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analisis_tda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_resultado_id')
                ->unique()
                ->constrained('encuestas_resultados')
                ->onDelete('cascade')
                ->comment('ID del resultado de la encuesta al que corresponde este análisis');

            // Puntuaciones
            $table->integer('puntuacion_inatencion')
                ->default(0)
                ->comment('Suma de puntuaciones de las preguntas de inatención');
            $table->integer('puntuacion_hiperactividad')
                ->default(0)
                ->comment('Suma de puntuaciones de las preguntas de hiperactividad');
            $table->integer('puntuacion_total')
                ->default(0)
                ->comment('Suma total de puntuaciones');

            // Síntomas significativos (puntuación ≥2); el umbral por dimensión
            // depende de la edad y se registra en la columna umbral_sintomas
            $table->integer('sintomas_inatencion')
                ->default(0)
                ->comment('Número de síntomas de inatención');
            $table->integer('sintomas_hiperactividad')
                ->default(0)
                ->comment('Número de síntomas de hiperactividad');
            $table->unsignedTinyInteger('umbral_sintomas')
                ->default(6)
                ->comment('Síntomas mínimos por dimensión exigidos según DSM-5: 5 desde los 17 años, 6 en menores');

            // Resultado del análisis
            $table->enum('resultado', [
                'tda_combinado',
                'tda_inatento',
                'tda_hiperactivo',
                'tda_posible',
                'no_tda'
            ])->default('no_tda')
                ->comment('Resultado del análisis: TDA combinado, TDA inatento, TDA hiperactivo, TDA posible, No TDA');

            // Porcentajes para visualización
            $table->decimal('porcentaje_inatencion', 5, 2)
                ->default(0)
                ->comment('Porcentaje de síntomas de inatención');
            $table->decimal('porcentaje_hiperactividad', 5, 2)
                ->default(0)
                ->comment('Porcentaje de síntomas de hiperactividad');

            // Descripción del resultado
            $table->text('descripcion')
                  ->nullable()
                  ->comment('Descripción detallada del resultado del análisis');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_tda');
    }
};
