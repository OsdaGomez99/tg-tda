<?php

namespace Tests\Feature\Listados;

use App\Models\Semestre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemestresListadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_los_semestres_existentes(): void
    {
        $this->actingAsAdmin();

        Semestre::factory()->create(['nombre' => '2026-2']);

        $response = $this->get(route('semestres.index'));

        $response->assertOk();
        $response->assertViewIs('pages.semestres.semestres-index');
        $response->assertSee('2026-2');
    }

    public function test_pagina_el_listado_de_semestres(): void
    {
        $this->actingAsAdmin();

        Semestre::factory()->count(12)->create();

        $response = $this->get(route('semestres.index'));

        $response->assertViewHas('semestres', fn ($semestres) => $semestres->count() === 10 && $semestres->total() === 12);
    }
}
