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
            $table->foreignId('semestre_id')
                ->nullable()
                ->constrained('semestres')
                ->onDelete('restrict')
                ->comment('Semestre en el que se respondió la encuesta');
            $table->string('nombre_estudiante')
                ->comment('Nombre completo del estudiante');
            $table->string('documento_estudiante')
                ->index()
                ->comment('Número de documento del estudiante');
            $table->integer('edad_estudiante')
                ->comment('Edad del estudiante');
            $table->enum('sexo_estudiante', ['M', 'F', 'O'])
                ->comment('Sexo del estudiante (M: Masculino, F: Femenino, O: Otro)');
            $table->foreignId('carrera_id')
                ->nullable()
                ->constrained('carreras')
                ->onDelete('set null')
                ->comment('ID de la carrera del estudiante');
            $table->unique(
                ['encuesta_id', 'documento_estudiante', 'semestre_id'],
                'encuestas_resultados_encuesta_doc_semestre_unique'
            );
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
