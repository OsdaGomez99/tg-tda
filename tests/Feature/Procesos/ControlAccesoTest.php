<?php

namespace Tests\Feature\Procesos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ControlAccesoTest extends TestCase
{
    use RefreshDatabase;

    public static function rutasProtegidasPorPermiso(): array
    {
        return [
            'preguntas' => ['preguntas.index', 'preguntas'],
            'encuestas' => ['encuestas.index', 'encuestas'],
            'estudiantes' => ['estudiantes.index', 'estudiantes'],
            'semestres' => ['semestres.index', 'semestres'],
            'usuarios' => ['usuarios.index', 'usuarios'],
        ];
    }

    #[DataProvider('rutasProtegidasPorPermiso')]
    public function test_un_invitado_es_redirigido_al_login(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    #[DataProvider('rutasProtegidasPorPermiso')]
    public function test_un_usuario_sin_el_permiso_requerido_es_rechazado(string $routeName): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->get(route($routeName));

        $response->assertRedirect(route('resumen'));
        $response->assertSessionHas('error', 'No tienes permiso para acceder a esta sección.');
    }

    #[DataProvider('rutasProtegidasPorPermiso')]
    public function test_un_usuario_con_el_permiso_especifico_puede_acceder(string $routeName, string $permiso): void
    {
        $this->actingAsUserWithPermissions([$permiso]);

        $this->get(route($routeName))->assertOk();
    }

    #[DataProvider('rutasProtegidasPorPermiso')]
    public function test_un_usuario_con_otro_permiso_distinto_sigue_siendo_rechazado(string $routeName, string $permiso): void
    {
        $otroPermiso = collect(['preguntas', 'encuestas', 'estudiantes', 'semestres', 'usuarios'])
            ->reject(fn ($p) => $p === $permiso)
            ->first();

        $this->actingAsUserWithPermissions([$otroPermiso]);

        $this->get(route($routeName))->assertRedirect(route('resumen'));
    }

    #[DataProvider('rutasProtegidasPorPermiso')]
    public function test_el_administrador_puede_acceder_a_todas_las_secciones(string $routeName): void
    {
        $this->actingAsAdmin();

        $this->get(route($routeName))->assertOk();
    }

    public function test_un_invitado_no_puede_ver_el_resumen(): void
    {
        $this->get(route('resumen'))->assertRedirect(route('login'));
    }

    public function test_cualquier_usuario_autenticado_puede_ver_el_resumen(): void
    {
        $this->actingAsUserWithoutPermissions();

        $this->get(route('resumen'))->assertOk();
    }

    public function test_una_peticion_json_sin_permiso_recibe_un_error_403(): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->getJson(route('preguntas.index'));

        $response->assertStatus(403);
        $response->assertJson(['message' => 'No tienes permiso para acceder a esta sección.']);
    }
}
