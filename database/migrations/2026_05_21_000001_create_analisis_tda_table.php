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
            $table->foreignId('encuesta_resultado_id')->unique()->constrained('encuestas_resultados')->onDelete('cascade');

            // Puntuaciones
            $table->integer('puntuacion_inatención')->default(0);
            $table->integer('puntuacion_hiperactividad')->default(0);
            $table->integer('puntuacion_total')->default(0);

            // Síntomas significativos (≥6 con puntuación ≥2)
            $table->integer('sintomas_inatención')->default(0);
            $table->integer('sintomas_hiperactividad')->default(0);

            // Resultado del análisis
            $table->enum('resultado', [
                'tda_combinado',
                'tda_inatento',
                'tda_hiperactivo',
                'tda_possible',
                'no_tda'
            ])->default('no_tda');

            // Porcentajes para visualización
            $table->decimal('porcentaje_inatención', 5, 2)->default(0);
            $table->decimal('porcentaje_hiperactividad', 5, 2)->default(0);

            // Descripción del resultado
            $table->text('descripcion')->nullable();

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
