<?php

namespace Tests\Feature\Formularios;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuariosFormularioTest extends TestCase
{
    use RefreshDatabase;

    public function test_guarda_un_nuevo_usuario_con_permisos(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('usuarios.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'secreto1',
            'permisos' => ['preguntas', 'encuestas'],
        ]);

        $response->assertRedirect(route('usuarios.index'));

        $usuario = User::where('email', 'nuevo@correo.com')->firstOrFail();
        $this->assertTrue(Hash::check('secreto1', $usuario->password));
        $this->assertEqualsCanonicalizing(['preguntas', 'encuestas'], $usuario->getPermissionNames()->all());
    }

    public function test_no_guarda_un_usuario_con_correo_duplicado(): void
    {
        $this->actingAsAdmin();

        User::factory()->create(['email' => 'existente@correo.com']);

        $response = $this->post(route('usuarios.store'), [
            'name' => 'Otro Usuario',
            'email' => 'existente@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'secreto1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_no_guarda_un_usuario_con_contrasenas_que_no_coinciden(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('usuarios.store'), [
            'name' => 'Usuario Prueba',
            'email' => 'prueba@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'prueba@correo.com']);
    }

    public function test_no_guarda_un_usuario_con_permiso_desconocido(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('usuarios.store'), [
            'name' => 'Usuario Prueba',
            'email' => 'prueba2@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'secreto1',
            'permisos' => ['modulo_inexistente'],
        ]);

        $response->assertSessionHasErrors('permisos.0');
    }

    public function test_actualiza_nombre_y_permisos_de_un_usuario(): void
    {
        $this->actingAsAdmin();

        $usuario = User::factory()->create();
        $usuario->givePermissionTo('preguntas');

        $response = $this->put(route('usuarios.update', $usuario), [
            'name' => 'Nombre Actualizado',
            'email' => $usuario->email,
            'permisos' => ['semestres'],
        ]);

        $response->assertRedirect(route('usuarios.index'));

        $usuario->refresh();
        $this->assertSame('Nombre Actualizado', $usuario->name);
        $this->assertEqualsCanonicalizing(['semestres'], $usuario->getPermissionNames()->all());
    }

    public function test_actualizar_usuario_sin_contrasena_conserva_la_actual(): void
    {
        $this->actingAsAdmin();

        $usuario = User::factory()->create();
        $hashOriginal = $usuario->password;

        $this->put(route('usuarios.update', $usuario), [
            'name' => $usuario->name,
            'email' => $usuario->email,
        ]);

        $this->assertSame($hashOriginal, $usuario->fresh()->password);
    }

    public function test_los_permisos_del_administrador_no_se_modifican_al_actualizar(): void
    {
        $this->actingAsAdmin();

        $administrador = User::factory()->create();
        $administrador->assignRole('Administrador');
        $permisosOriginales = $administrador->getPermissionNames()->sort()->values()->all();

        $this->put(route('usuarios.update', $administrador), [
            'name' => $administrador->name,
            'email' => $administrador->email,
            'permisos' => [],
        ]);

        $this->assertSame($permisosOriginales, $administrador->fresh()->getPermissionNames()->sort()->values()->all());
    }

    public function test_elimina_un_usuario(): void
    {
        $this->actingAsAdmin();

        $usuario = User::factory()->create();

        $response = $this->delete(route('usuarios.destroy', $usuario));

        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }

    public function test_un_usuario_no_puede_eliminarse_a_si_mismo(): void
    {
        $admin = $this->actingAsAdmin();

        $response = $this->delete(route('usuarios.destroy', $admin));

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
