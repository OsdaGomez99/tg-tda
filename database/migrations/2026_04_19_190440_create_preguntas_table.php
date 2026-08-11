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
        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')
                  ->nullable()
                  ->comment('Código único para identificar la pregunta, ej: P1, P2, etc.');
            $table->string('nombre')
                  ->comment('Texto de la pregunta');
            $table->text('descripcion')
                  ->nullable()
                  ->comment('Descripción o explicación de la pregunta');
            $table->enum('tipo_tda', ['I', 'H'])
                  ->nullable()
                  ->comment('I = Inatención, H = Hiperactividad/Impulsividad');
            $table->boolean('estado')
                  ->default(true)
                  ->comment('Indica si la pregunta está activa o inactiva');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas');
    }
};
