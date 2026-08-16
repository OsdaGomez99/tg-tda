@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Tabla de Estudiantes -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div
                class="flex flex-col gap-4 border-b border-gray-200 p-6 dark:border-gray-800 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Listado de estudiantes</h3>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ $estudiantes->total() }} estudiante(s) {{ $search !== '' || $carreraId !== '' ? 'encontrado(s)' : 'en total' }}
                    </p>
                </div>
                <form method="GET" action="{{ route('estudiantes.index') }}"
                    class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Buscar por nombre o documento..."
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:w-64">
                    <select name="carrera_id" onchange="this.form.submit()"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:w-56">
                        <option value="">Todas las carreras</option>
                        @foreach ($carreras as $carrera)
                            <option value="{{ $carrera->id }}" {{ (string) $carreraId === (string) $carrera->id ? 'selected' : '' }}>
                                {{ $carrera->nombre }}
                            </option>

                        @endforeach

                    </select>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                            Buscar
                        </button>
                        @if ($search !== '' || $carreraId !== '')
                            <a href="{{ route('estudiantes.index') }}"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nombre</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Documento</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Carrera</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Encuestas
                                        respondidas</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Semestres
                                        participados</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($estudiantes as $estudiante)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $estudiante->nombre_estudiante }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $estudiante->documento_estudiante }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $estudiante->carrera->nombre ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $resumen[$estudiante->documento_estudiante]->total_encuestas ?? 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $resumen[$estudiante->documento_estudiante]->total_semestres ?? 1 }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('estudiantes.show', $estudiante->documento_estudiante) }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-700/50 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                        Ver estadísticas
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                    @if ($search !== '' || $carreraId !== '')
                                        No se encontraron estudiantes con esos criterios de búsqueda
                                    @else
                                        No hay estudiantes registrados aún
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($estudiantes->hasPages())
                <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                    {{ $estudiantes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
