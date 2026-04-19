<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Inatención'],
            ['nombre' => 'Hiperactividad/Impulsividad'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate($categoria);
        }
    }
}
