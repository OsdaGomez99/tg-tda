<?php

namespace Database\Seeders;

use App\Models\Encuesta;
use App\Models\Pregunta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EncuestasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encuesta = [
            'codigo_acceso' => 'ABCD',
            'nombre'        => 'Encuesta TDA',
            'descripcion'   => 'Esta encuesta está diseñada para evaluar el Trastorno por Déficit de Atención (TDA) en estudiantes. Contiene preguntas específicas que ayudan a identificar los síntomas y comportamientos asociados con el TDA.',
            'usuario_id'    => 1,
        ];

        $sync = [];

        //preguntas ids de las preguntas disponibles en la base de datos
        $preguntaIds = Pregunta::all()->pluck('id')->toArray();
        foreach ($preguntaIds as $index => $preguntaId) {
            $sync[$preguntaId] = ['orden' => $index + 1];
        }

        // Crar encuesta y asignar preguntas
        $encuesta = Encuesta::create(
            [
                'codigo_acceso' => $encuesta['codigo_acceso'],
                'nombre' => $encuesta['nombre'],
                'descripcion' => $encuesta['descripcion'],
                'usuario_id' => $encuesta['usuario_id']
            ]
        );
        $encuesta->preguntas()->sync($sync);
    }
}
