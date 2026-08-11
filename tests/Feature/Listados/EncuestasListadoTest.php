<?php

namespace Tests\Feature\Listados;

use App\Models\Encuesta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncuestasListadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_las_encuestas_existentes(): void
    {
        $this->actingAsAdmin();

        Encuesta::factory()->create(['nombre' => 'Encuesta visible en listado']);

        $response = $this->get(route('encuestas.index'));

        $response->assertOk();
        $response->assertViewIs('pages.encuestas.encuestas-index');
        $response->assertSee('Encuesta visible en listado');
    }

    public function test_filtra_encuestas_por_nombre(): void
    {
        $this->actingAsAdmin();

        Encuesta::factory()->create(['nombre' => 'Encuesta de matemáticas']);
        Encuesta::factory()->create(['nombre' => 'Encuesta de historia']);

        $response = $this->get(route('encuestas.index', ['search' => 'matemáticas']));

        $response->assertSee('Encuesta de matemáticas');
        $response->assertDontSee('Encuesta de historia');
    }

    public function test_filtra_encuestas_por_usuario_creador(): void
    {
        $this->actingAsAdmin();

        $creador = User::factory()->create(['name' => 'Profesor Responsable']);
        Encuesta::factory()->create(['nombre' => 'Encuesta A', 'usuario_id' => $creador->id]);
        Encuesta::factory()->create(['nombre' => 'Encuesta B']);

        $response = $this->get(route('encuestas.index', ['search' => 'Profesor Responsable']));

        $response->assertSee('Encuesta A');
        $response->assertDontSee('Encuesta B');
    }

    public function test_pagina_el_listado_de_encuestas(): void
    {
        $this->actingAsAdmin();

        Encuesta::factory()->count(12)->create();

        $response = $this->get(route('encuestas.index'));

        $response->assertViewHas('encuestas', fn ($encuestas) => $encuestas->count() === 10 && $encuestas->total() === 12);
    }
}
