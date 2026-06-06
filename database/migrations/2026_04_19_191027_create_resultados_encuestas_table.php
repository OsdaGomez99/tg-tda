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
        Schema::create('encuestas_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')
                  ->constrained('encuestas')
                  ->onDelete('cascade')
                  ->comment('ID de la encuesta realizada');
            $table->string('nombre_estudiante')
                  ->comment('Nombre completo del estudiante');
            $table->integer('edad_estudiante')
                  ->comment('Edad del estudiante');
            $table->enum('sexo_estudiante', ['M', 'F', 'O'])
                  ->comment('Sexo del estudiante (M: Masculino, F: Femenino, O: Otro)');
            $table->foreignId('carrera_id')
                  ->nullable()
                  ->constrained('carreras')
                  ->onDelete('set null')
                  ->comment('ID de la carrera del estudiante');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas_resultados');
    }
};
