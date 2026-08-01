<?php

namespace App\Http\Controllers;

use App\Helpers\MenuHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Mostrar lista de usuarios
     */
    public function index(): View
    {
        $usuarios = User::orderBy('name')->get();

        return view('pages.usuarios.usuarios-index', [
            'title' => 'Usuarios',
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Mostrar formulario de crear usuario
     */
    public function create(): View
    {
        return view('pages.usuarios.usuarios-create', [
            'title' => 'Nuevo Usuario',
            'modulosPermisos' => MenuHelper::getPermissionModules(),
        ]);
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,filter|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'permisos' => 'array',
            'permisos.*' => Rule::in(array_keys(MenuHelper::getPermissionModules())),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncPermissions($validated['permisos'] ?? []);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Editar usuario
     */
    public function show(User $user): View
    {
        return view('pages.usuarios.usuarios-show', [
            'title' => 'Editar Usuario',
            'usuario' => $user,
            'modulosPermisos' => MenuHelper::getPermissionModules(),
            'permisosUsuario' => $user->getPermissionNames()->toArray(),
            'esAdministrador' => $user->hasRole('Administrador'),
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email:rfc,filter',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'permisos' => 'array',
            'permisos.*' => Rule::in(array_keys(MenuHelper::getPermissionModules())),
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // El rol Administrador siempre conserva acceso total; sus permisos no se tocan aquí.
        if (! $user->hasRole('Administrador')) {
            $user->syncPermissions($validated['permisos'] ?? []);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
