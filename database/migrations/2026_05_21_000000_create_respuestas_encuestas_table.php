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
            $table->foreignId('encuesta_resultado_id')->constrained('encuestas_resultados')->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained('preguntas')->onDelete('cascade');
            $table->integer('puntuacion')->comment('Escala 0-3: Nunca, A veces, Con frecuencia, Muy frecuentemente');
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
