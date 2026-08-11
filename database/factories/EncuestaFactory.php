<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Encuesta>
 */
class EncuestaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => 'Encuesta ' . Str::upper(Str::random(8)),
            'codigo_acceso' => Str::upper(Str::random(4)),
            'descripcion' => fake()->sentence(),
            'usuario_id' => User::factory(),
        ];
    }
}
