<?php

namespace Tests\Feature\Listados;

use App\Models\Pregunta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreguntasListadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_las_preguntas_existentes(): void
    {
        $this->actingAsAdmin();

        Pregunta::factory()->create(['nombre' => 'Pregunta visible en el listado']);

        $response = $this->get(route('preguntas.index'));

        $response->assertOk();
        $response->assertViewIs('pages.preguntas.preguntas-index');
        $response->assertSee('Pregunta visible en el listado');
    }

    public function test_filtra_preguntas_por_busqueda_de_nombre(): void
    {
        $this->actingAsAdmin();

        Pregunta::factory()->create(['nombre' => 'Olvida objetos con frecuencia']);
        Pregunta::factory()->create(['nombre' => 'Interrumpe conversaciones ajenas']);

        $response = $this->get(route('preguntas.index', ['search' => 'Olvida']));

        $response->assertSee('Olvida objetos con frecuencia');
        $response->assertDontSee('Interrumpe conversaciones ajenas');
    }

    public function test_filtra_preguntas_por_busqueda_de_codigo(): void
    {
        $this->actingAsAdmin();

        $pregunta = Pregunta::factory()->create();

        $response = $this->get(route('preguntas.index', ['search' => $pregunta->codigo]));

        $response->assertSee($pregunta->nombre);
    }

    public function test_pagina_el_listado_de_preguntas(): void
    {
        $this->actingAsAdmin();

        Pregunta::factory()->count(15)->create();

        $response = $this->get(route('preguntas.index'));

        $response->assertViewHas('preguntas', fn ($preguntas) => $preguntas->count() === 10 && $preguntas->total() === 15);
    }
}
