<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador',
                'email' => 'admin@correo.com',
                'password' => bcrypt('123456789'),
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::firstOrCreate(
                ['email' => $usuario['email']],
                $usuario
            );
        }
    }
}
