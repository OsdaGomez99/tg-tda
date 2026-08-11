<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crea (o reutiliza) el rol Administrador con todos los permisos del
     * sistema, autentica un usuario con ese rol y lo devuelve.
     */
    protected function actingAsAdmin(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Administrador');

        $this->actingAs($user);

        return $user;
    }

    /**
     * Autentica un usuario sin ningún permiso asignado, útil para probar
     * que el middleware de permisos bloquea el acceso.
     */
    protected function actingAsUserWithoutPermissions(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    /**
     * Autentica un usuario con únicamente los permisos indicados.
     */
    protected function actingAsUserWithPermissions(array $permissions): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        $this->actingAs($user);

        return $user;
    }
}
