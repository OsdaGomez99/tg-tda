<?php

namespace Database\Seeders;

use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CarrerasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carreras = [
            ['nombre' => 'Administración de Empresas'],
            ['nombre' => 'Administración Mención Banca y Finanzas'],
            ['nombre' => 'Ciencias Ambientales'],
            ['nombre' => 'Ciencias Fiscales'],
            ['nombre' => 'Contaduría Pública'],
            ['nombre' => 'Educación en Ciencias: Física, Química y Biología'],
            ['nombre' => 'Educación Integral'],
            ['nombre' => 'Educación. Mención Educación Física, Deporte y Recreación'],
            ['nombre' => 'Educación. Mención Lengua y Literatura'],
            ['nombre' => 'Educación. Mención Matemática'],
            ['nombre' => 'Ingeniería de Producción Animal'],
            ['nombre' => 'Ingeniería en Industrias Forestales'],
            ['nombre' => 'Ingeniería en Informática'],
            ['nombre' => 'Ingeniería en Materiales'],
            ['nombre' => 'Ingeniería Industrial'],
            ['nombre' => 'Licenciatura en Gestión de Alojamiento Turístico'],
            ['nombre' => 'T.S.U. Empresa de Alojamiento Turístico'],
            ['nombre' => 'T.S.U. Turismo'],
            ['nombre' => 'Tecnología en Producción Agropecuaria'],
        ];

        foreach ($carreras as $carrera) {
            Carrera::updateOrCreate($carrera);
        }
    }
}
