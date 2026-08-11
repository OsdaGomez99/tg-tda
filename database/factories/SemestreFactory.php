<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semestre>
 */
class SemestreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->numerify('####-#'),
            'activo' => false,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => ['activo' => true]);
    }
}
