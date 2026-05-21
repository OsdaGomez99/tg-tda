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
            $table->string('nombre');
            $table->text('ejemplo')->nullable()->comment('Ejemplo o explicación de la pregunta');
            $table->foreignId('categoria_id')->constrained('categorias')->nullable();
            $table->char('tipo_tda', 1)->nullable()->comment('I = Inatención, H = Hiperactividad/Impulsividad');
            $table->boolean('estado')->default(true);
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
