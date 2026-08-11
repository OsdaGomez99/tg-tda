<?php

namespace Database\Factories;

use App\Models\Carrera;
use App\Models\Encuesta;
use App\Models\Semestre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EncuestaResultado>
 */
class EncuestaResultadoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'encuesta_id' => Encuesta::factory(),
            'semestre_id' => Semestre::factory(),
            'nombre_estudiante' => fake()->name(),
            'documento_estudiante' => fake()->unique()->regexify('[A-Z]\d{8}'),
            'edad_estudiante' => fake()->numberBetween(16, 30),
            'sexo_estudiante' => fake()->randomElement(['M', 'F', 'O']),
            'carrera_id' => Carrera::factory(),
        ];
    }
}
