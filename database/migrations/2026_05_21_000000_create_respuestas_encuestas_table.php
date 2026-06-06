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
        Schema::create('respuestas_encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_resultado_id')
                  ->constrained('encuestas_resultados')
                  ->onDelete('cascade')
                  ->comment('ID del resultado de la encuesta al que pertenece esta respuesta');
            $table->foreignId('pregunta_id')
                  ->constrained('preguntas')
                  ->onDelete('cascade')
                  ->comment('ID de la pregunta a la que corresponde esta respuesta');
            $table->integer('puntuacion')
                  ->comment('Escala 0-3: Nunca, A veces, Con frecuencia, Muy frecuentemente')
                  ->comment('Puntuación de la respuesta: 0 (Nunca), 1 (A veces), 2 (Con frecuencia), 3 (Muy frecuentemente)');
            $table->timestamps();

            // Índice compuesto para búsquedas rápidas
            $table->unique(['encuesta_resultado_id', 'pregunta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_encuestas');
    }
};
