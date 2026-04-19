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
                'id'           => 1,
                'nombre'       => 'Con frecuencia no presta atención suficiente a los detalles o comete errores por descuido en las tareas o en otras actividades.',
                'descripcion'  => 'Ej: pasa por alto detalles, el trabajo es impreciso.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 2,
                'nombre'       => 'Con frecuencia tiene dificultades para mantener la atención en tareas o actividades recreativas.',
                'descripcion'  => 'Ej: le cuesta mantenerse enfocado durante una clase o conversación larga.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 3,
                'nombre'       => 'Con frecuencia parece no escuchar cuando se le habla directamente.',
                'descripcion'  => 'Ej: su mente parece estar en otro lugar, incluso sin ninguna distracción aparente.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 4,
                'nombre'       => 'Con frecuencia no sigue las instrucciones y no termina las tareas escolares, los quehaceres o las responsabilidades laborales.',
                'descripcion'  => 'Ej: empieza tareas pero se distrae rápidamente.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 5,
                'nombre'       => 'Con frecuencia tiene dificultades para organizar tareas y actividades.',
                'descripcion'  => 'Ej: le cuesta gestionar tareas secuenciales o mantener el orden en sus cosas.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 6,
                'nombre'       => 'Con frecuencia evita, le disgusta o es renuente a dedicarse a tareas que requieren un esfuerzo mental sostenido.',
                'descripcion'  => 'Ej: deberes escolares o trabajo administrativo.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 7,
                'nombre'       => 'Con frecuencia pierde objetos necesarios para tareas o actividades.',
                'descripcion'  => 'Ej: pierde materiales escolares, llaves, lentes, teléfono.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 8,
                'nombre'       => 'Con frecuencia se distrae fácilmente por estímulos externos.',
                'descripcion'  => 'Ej: pensamientos no relacionados o ruidos del entorno.',
                'categoria_id' => 1,
            ],
            [
                'id'           => 9,
                'nombre'       => 'Con frecuencia es olvidadizo en las actividades cotidianas.',
                'descripcion'  => 'Ej: hacer las tareas, las diligencias, responder llamadas.',
                'categoria_id' => 1,
            ],

            // Hiperactividad / Impulsividad (9 síntomas DSM-5)
            [
                'id'           => 10,
                'nombre'       => 'Con frecuencia juguetea con o golpea las manos o los pies, o se retuerce en el asiento.',
                'descripcion'  => 'Ej: no puede estar quieto en el asiento.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 11,
                'nombre'       => 'Con frecuencia se levanta en situaciones en que se espera que permanezca sentado.',
                'descripcion'  => 'Ej: se levanta en el aula o en la oficina.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 12,
                'nombre'       => 'Con frecuencia corretea o trepa en situaciones en las que no resulta apropiado.',
                'descripcion'  => 'Nota: en adultos puede limitarse a una sensación de inquietud.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 13,
                'nombre'       => 'Con frecuencia es incapaz de jugar o de ocuparse tranquilamente en actividades recreativas.',
                'descripcion'  => 'Ej: le cuesta actividades tranquilas y sosegadas.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 14,
                'nombre'       => 'Con frecuencia está "ocupado", actuando como si "lo impulsara un motor".',
                'descripcion'  => 'Ej. es incapaz de estar quieto durante mucho tiempo.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 15,
                'nombre'       => 'Con frecuencia habla excesivamente.',
                'descripcion'  => 'Ej: habla sin parar en situaciones sociales o de trabajo.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 16,
                'nombre'       => 'Con frecuencia responde inesperadamente o antes de que se haya concluido una pregunta.',
                'descripcion'  => 'Ej: termina la frase de los demás.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 17,
                'nombre'       => 'Con frecuencia le es difícil esperar su turno.',
                'descripcion'  => 'Ej. mientras espera en una fila.',
                'categoria_id' => 2,
            ],
            [
                'id'           => 18,
                'nombre'       => 'Con frecuencia interrumpe o se inmiscuye con otros.',
                'descripcion'  => 'Ej: se entromete en conversaciones, juegos o actividades.',
                'categoria_id' => 2,
            ],
        ];

        foreach ($preguntas as $pregunta) {
            Pregunta::updateOrCreate(['id' => $pregunta['id']], $pregunta);
        }
    }
}
