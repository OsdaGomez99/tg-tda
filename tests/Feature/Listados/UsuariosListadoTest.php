<?php

namespace Tests\Feature\Listados;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosListadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_los_usuarios_existentes(): void
    {
        $this->actingAsAdmin();

        User::factory()->create(['name' => 'Usuario Listado', 'email' => 'listado@correo.com']);

        $response = $this->get(route('usuarios.index'));

        $response->assertOk();
        $response->assertViewIs('pages.usuarios.usuarios-index');
        $response->assertSee('Usuario Listado');
    }

    public function test_filtra_usuarios_por_nombre_o_correo(): void
    {
        $this->actingAsAdmin();

        User::factory()->create(['name' => 'Ana Torres', 'email' => 'ana@correo.com']);
        User::factory()->create(['name' => 'Luis Pérez', 'email' => 'luis@correo.com']);

        $response = $this->get(route('usuarios.index', ['search' => 'Ana']));

        $response->assertSee('Ana Torres');
        $response->assertDontSee('Luis Pérez');
    }

    public function test_pagina_el_listado_de_usuarios(): void
    {
        $this->actingAsAdmin();

        User::factory()->count(12)->create();

        $response = $this->get(route('usuarios.index'));

        $response->assertViewHas('usuarios', fn ($usuarios) => $usuarios->count() === 10 && $usuarios->total() === 13);
    }
}
