@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <x-common.component-card title="Editar usuario">

            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nombre
                    </label>
                    <input type="text" name="name" placeholder="Ingrese el nombre del usuario..."
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('name') border-red-500 @enderror"
                        value="{{ old('name', $usuario->name) }}" />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Correo
                    </label>
                    <input type="email" name="email" placeholder="Ingrese el correo del usuario..."
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-red-500 @enderror"
                        value="{{ old('email', $usuario->email) }}" />
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5 grid grid-cols-2 gap-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nueva contraseña
                        </label>
                        <input type="password" name="password" placeholder="Dejar en blanco para no cambiarla"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password') border-red-500 @enderror" />
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Confirmar contraseña
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Repita la nueva contraseña"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-white/90">Permisos de acceso</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Selecciona a qué secciones del sistema puede acceder este usuario.
                    </p>

                    @if ($esAdministrador)
                        <p
                            class="mt-4 rounded-lg border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700 dark:border-brand-800 dark:bg-brand-500/10 dark:text-brand-300">
                            Este usuario tiene el rol <strong>Administrador</strong> y tiene acceso a todas las rutas
                            del sistema.
                        </p>
                    @else
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($modulosPermisos as $modulo => $etiqueta)
                                <label
                                    class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="permisos[]" value="{{ $modulo }}"
                                        class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700"
                                        {{ in_array($modulo, old('permisos', $permisosUsuario)) ? 'checked' : '' }}>
                                    {{ $etiqueta }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Actualizar usuario
                    </button>
                    <a href="{{ route('usuarios.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                        Cancelar
                    </a>
                </div>
            </form>

        </x-common.component-card>
    </div>
@endsection
