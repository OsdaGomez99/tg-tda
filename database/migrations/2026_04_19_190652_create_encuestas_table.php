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
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')
                  ->nullable()
                  ->comment('Código único para identificar la encuesta, ej: E1, E2, etc.');
            $table->string('codigo_acceso')
                  ->nullable()
                  ->unique()
                  ->comment('Código de acceso para participar en la encuesta');
            $table->string('nombre')
                  ->unique()
                  ->comment('Nombre de la encuesta');
            $table->text('descripcion')
                  ->nullable()
                  ->comment('Descripción de la encuesta');
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->comment('ID del usuario que creó la encuesta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
