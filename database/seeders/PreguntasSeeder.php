<?php

namespace Database\Seeders;

use App\Models\Pregunta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreguntasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preguntas = [
            // Inatención (9 síntomas DSM-5)
            [
                'nombre'       => 'Con frecuencia no presta atención suficiente a los detalles o comete errores por descuido en las tareas o en otras actividades.',
                'descripcion'  => 'Ej: Pasa por alto detalles, el trabajo es impreciso.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia tiene dificultades para mantener la atención en tareas o actividades recreativas.',
                'descripcion'  => 'Ej: Le cuesta mantenerse enfocado durante una clase o conversación larga.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia parece no escuchar cuando se le habla directamente.',
                'descripcion'  => 'Ej: Su mente parece estar en otro lugar, incluso sin ninguna distracción aparente.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia no sigue las instrucciones y no termina las tareas escolares, los quehaceres o las responsabilidades laborales.',
                'descripcion'  => 'Ej: Empieza tareas pero se distrae rápidamente.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia tiene dificultades para organizar tareas y actividades.',
                'descripcion'  => 'Ej: Le cuesta gestionar tareas secuenciales o mantener el orden en sus cosas.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia evita, le disgusta o es renuente a dedicarse a tareas que requieren un esfuerzo mental sostenido.',
                'descripcion'  => 'Ej: Deberes escolares o trabajo administrativo.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia pierde objetos necesarios para tareas o actividades.',
                'descripcion'  => 'Ej: Pierde materiales escolares, llaves, lentes, teléfono.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia se distrae fácilmente por estímulos externos.',
                'descripcion'  => 'Ej: Pensamientos no relacionados o ruidos del entorno.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia es olvidadizo en las actividades cotidianas.',
                'descripcion'  => 'Ej: Hacer las tareas, las diligencias, responder llamadas.',
                'tipo_tda'     => 'I',
                'estado'       => true,
            ],

            // Hiperactividad / Impulsividad (9 síntomas DSM-5)
            [
                'nombre'       => 'Con frecuencia juguetea con o golpea las manos o los pies, o se retuerce en el asiento.',
                'descripcion'  => 'Ej: No puede estar quieto en el asiento.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia se levanta en situaciones en que se espera que permanezca sentado.',
                'descripcion'  => 'Ej: Se levanta en el aula o en la oficina.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia corretea o trepa en situaciones en las que no resulta apropiado.',
                'descripcion'  => 'Nota: En adultos puede limitarse a una sensación de inquietud.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia es incapaz de jugar o de ocuparse tranquilamente en actividades recreativas.',
                'descripcion'  => 'Ej: Le cuesta actividades tranquilas y sosegadas.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia está "ocupado", actuando como si "lo impulsara un motor".',
                'descripcion'  => 'Ej: Es incapaz de estar quieto durante mucho tiempo.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia habla excesivamente.',
                'descripcion'  => 'Ej: Habla sin parar en situaciones sociales o de trabajo.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia responde inesperadamente o antes de que se haya concluido una pregunta.',
                'descripcion'  => 'Ej: Termina la frase de los demás.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia le es difícil esperar su turno.',
                'descripcion'  => 'Ej: Mientras espera en una fila.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
            [
                'nombre'       => 'Con frecuencia interrumpe o se inmiscuye con otros.',
                'descripcion'  => 'Ej: Se entromete en conversaciones, juegos o actividades.',
                'tipo_tda'     => 'H',
                'estado'       => true,
            ],
        ];

        foreach ($preguntas as $pregunta) {
            Pregunta::firstOrCreate(
                ['nombre' => $pregunta['nombre']],
                $pregunta
            );
        }
    }
}
