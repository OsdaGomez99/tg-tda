@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div id="successToast" class="fixed inset-x-0 top-20 z-[9999] flex justify-center px-4">
                <div class="w-full max-w-md">
                    <div
                        class="relative rounded-xl border border-green-200 bg-white/95 shadow-xl backdrop-blur-sm dark:border-green-500/30 dark:bg-slate-900/95">
                        <x-ui.alert variant="success" title="Éxito" message="{{ session('success') }}" />
                        <button id="closeSuccessToast" type="button"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-gray-700 shadow-sm hover:bg-white dark:bg-gray-900/90 dark:text-gray-200">
                            ×
                        </button>
                    </div>
                </div>
            </div>
        @elseif (session('error'))
            <div id="errorToast" class="fixed inset-x-0 top-20 z-[9999] flex justify-center px-4">
                <div class="w-full max-w-md">
                    <div
                        class="relative rounded-xl border border-red-200 bg-white/95 shadow-xl backdrop-blur-sm dark:border-red-500/30 dark:bg-slate-900/95">
                        <x-ui.alert variant="error" title="Error" message="{{ session('error') }}" />
                        <button id="closeErrorToast" type="button"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-gray-700 shadow-sm hover:bg-white dark:bg-gray-900/90 dark:text-gray-200">
                            ×
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Nuevo semestre</h3>
            <form action="{{ route('semestres.store') }}" method="POST" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start">
                @csrf
                <div class="flex-1">
                    <input type="text" name="nombre" placeholder="Ej: 2026-2" value="{{ old('nombre') }}" maxlength="20"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Crear semestre
                </button>
            </form>
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Semestres</h3>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Semestre</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Estado</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Respuestas</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Fecha de creación</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse ($semestres as $semestre)
                            <tr>
                                <td class="px-6 py-3.5">
                                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                        {{ $semestre->nombre }}
                                    </p>
                                </td>
                                <td class="px-6 py-3.5">
                                    @if ($semestre->activo)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $semestre->encuesta_resultados_count }}
                                    </p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $semestre->created_at->format('d/m/Y') }}
                                    </p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2">
                                        @unless ($semestre->activo)
                                            <form action="{{ route('semestres.activar', $semestre) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                    Activar
                                                </button>
                                            </form>
                                        @endunless
                                        @if ($semestre->encuesta_resultados_count === 0)
                                            <form action="{{ route('semestres.destroy', $semestre) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" data-delete-trigger
                                                    class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700/50 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No hay semestres registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirmar eliminación</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                ¿Está seguro de que desea eliminar este semestre? Esta acción no se puede deshacer.
            </p>
            <div class="mt-6 flex gap-3">
                <button type="button" id="cancelDeleteBtn"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Cancelar
                </button>
                <button type="button" id="confirmDeleteBtn"
                    class="flex-1 rounded-lg bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <script>
        let deleteFormToSubmit = null;

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-delete-trigger]').forEach(button => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    deleteFormToSubmit = button.closest('form');
                    document.getElementById('deleteConfirmModal').classList.remove('hidden');
                });
            });

            document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
                document.getElementById('deleteConfirmModal').classList.add('hidden');
                deleteFormToSubmit = null;
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                if (deleteFormToSubmit) {
                    deleteFormToSubmit.submit();
                }
            });

            const successToast = document.getElementById('successToast');
            const closeSuccessToast = document.getElementById('closeSuccessToast');

            if (successToast) {
                const hideToast = () => successToast.classList.add('hidden');
                const timer = setTimeout(hideToast, 5000);

                if (closeSuccessToast) {
                    closeSuccessToast.addEventListener('click', () => {
                        clearTimeout(timer);
                        hideToast();
                    });
                }
            }
        });
    </script>
@endsection
