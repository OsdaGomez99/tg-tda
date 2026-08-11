<?php

namespace Tests\Feature\Formularios;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFormularioTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_el_formulario_de_inicio_de_sesion(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_muestra_el_formulario_de_registro(): void
    {
        $this->get(route('signup'))->assertOk();
    }

    public function test_inicia_sesion_con_credenciales_validas(): void
    {
        $user = User::factory()->create([
            'email' => 'valido@correo.com',
            'password' => Hash::make('secreto1'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'valido@correo.com',
            'password' => 'secreto1',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['accessToken', 'fullname', 'email']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_no_inicia_sesion_con_contrasena_incorrecta(): void
    {
        User::factory()->create([
            'email' => 'valido@correo.com',
            'password' => Hash::make('secreto1'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'valido@correo.com',
            'password' => 'incorrecta',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['errors' => ['Las credenciales proporcionadas son incorrectas.']]);
        $this->assertGuest();
    }

    public function test_no_inicia_sesion_con_un_correo_no_registrado(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'inexistente@correo.com',
            'password' => 'secreto1',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['El correo no se encuentra registrado.']);
    }

    public function test_no_inicia_sesion_sin_datos(): void
    {
        $response = $this->post(route('login'), []);

        $response->assertStatus(422);
    }

    public function test_registra_un_nuevo_usuario(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Usuario Nuevo',
            'email' => 'nuevo@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'secreto1',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['accessToken', 'fullname', 'email', 'permissions']);

        $this->assertDatabaseHas('users', ['email' => 'nuevo@correo.com']);
        $usuario = User::where('email', 'nuevo@correo.com')->firstOrFail();
        $this->assertTrue(Hash::check('secreto1', $usuario->password));
    }

    public function test_no_registra_un_usuario_con_correo_ya_existente(): void
    {
        User::factory()->create(['email' => 'existente@correo.com']);

        $response = $this->postJson(route('register'), [
            'name' => 'Otro Usuario',
            'email' => 'existente@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'secreto1',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_no_registra_un_usuario_con_contrasenas_que_no_coinciden(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Usuario Nuevo',
            'email' => 'nuevo2@correo.com',
            'password' => 'secreto1',
            'password_confirmation' => 'otra',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['email' => 'nuevo2@correo.com']);
    }
}
