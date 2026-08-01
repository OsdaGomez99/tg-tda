<?php

namespace Database\Seeders;

use App\Helpers\MenuHelper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Crea un permiso por cada módulo del sistema y un rol "Administrador"
     * con acceso a todos ellos.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(MenuHelper::getPermissionModules()) as $modulo) {
            Permission::firstOrCreate(['name' => $modulo, 'guard_name' => 'web']);
        }

        $administrador = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $administrador->syncPermissions(Permission::all());
    }
}
