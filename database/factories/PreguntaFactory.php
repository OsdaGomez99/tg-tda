<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pregunta>
 */
class PreguntaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->sentence(8),
            'descripcion' => fake()->sentence(),
            'estado' => true,
            'tipo_tda' => fake()->randomElement(['I', 'H']),
        ];
    }

    public function inatencion(): static
    {
        return $this->state(fn (array $attributes) => ['tipo_tda' => 'I']);
    }

    public function hiperactividad(): static
    {
        return $this->state(fn (array $attributes) => ['tipo_tda' => 'H']);
    }

    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => false]);
    }
}
