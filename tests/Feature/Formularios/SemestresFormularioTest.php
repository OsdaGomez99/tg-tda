<?php

namespace Tests\Feature\Formularios;

use App\Models\Semestre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemestresFormularioTest extends TestCase
{
    use RefreshDatabase;

    public function test_guarda_un_nuevo_semestre(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('semestres.store'), [
            'nombre' => '2026-1',
        ]);

        $response->assertRedirect(route('semestres.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('semestres', ['nombre' => '2026-1', 'activo' => false]);
    }

    public function test_no_guarda_un_semestre_sin_nombre(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('semestres.store'), ['nombre' => '']);

        $response->assertSessionHasErrors('nombre');
        $this->assertDatabaseCount('semestres', 0);
    }

    public function test_no_guarda_un_semestre_con_nombre_duplicado(): void
    {
        $this->actingAsAdmin();

        Semestre::factory()->create(['nombre' => '2026-1']);

        $response = $this->post(route('semestres.store'), ['nombre' => '2026-1']);

        $response->assertSessionHasErrors('nombre');
        $this->assertDatabaseCount('semestres', 1);
    }
}
