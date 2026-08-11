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
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Encuestas</h3>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ $encuestas->total() }} encuesta(s) {{ !empty($search) ? 'encontrada(s)' : 'en total' }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden xl:block">
                        <form action="{{ route('encuestas.index') }}" method="GET" class="flex items-center gap-2">
                            <div class="relative">
                                <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2">
                                    <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                                            fill="" />
                                    </svg>
                                </span>
                                <input id="searchInput" type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Busqueda por código, nombre o usuario"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-12 pr-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[430px]" />
                                @if (!empty($search))
                                    <a href="{{ route('encuestas.index') }}"
                                        class="absolute -translate-y-1/2 right-4 top-1/2 text-xs font-medium text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                        ×
                                    </a>
                                @endif
                            </div>
                            <x-ui.button id="searchButton" size="sm" variant="outline" type="submit"
                                :disabled="empty($search)">Buscar</x-ui.button>
                        </form>
                    </div>
                    <a href="{{ route('encuestas.create') }}">
                        <x-ui.button size="sm" variant="primary">Nueva encuesta</x-ui.button>
                    </a>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Código</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nombre</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Usuario
                                        responsable</p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Preguntas
                                    </p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Fecha de creación
                                    </p>
                                </div>
                            </th>
                            <th class="px-6 py-3">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Ruta de acceso</p>
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
                        @forelse ($encuestas as $encuesta)
                            <tr>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $encuesta->codigo }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $encuesta->nombre }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $encuesta->usuario?->name ?? 'Sin usuario' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $encuesta->preguntas->count() }} preguntas
                                            </span>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $encuesta->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="max-w-[420px] overflow-hidden">
                                        <a href="{{ route('encuestas.public.iniciar', ['codigo_acceso' => $encuesta->codigo_acceso]) }}"
                                            class="block min-w-0 truncate text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            target="_blank" rel="noopener noreferrer">
                                            {{ route('encuestas.public.iniciar', ['codigo_acceso' => $encuesta->codigo_acceso]) }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <a href={{ route('encuestas.show', $encuesta) }}
                                            class="text-blue-600 hover:text-blue-800 text-theme-sm">
                                            <x-ui.button size="xs" variant="outline">Editar</x-ui.button>
                                        </a>
                                        <form action="{{ route('encuestas.destroy', $encuesta) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-delete-trigger
                                                class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700/50 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                                Eliminar
                                            </button>
                                        </form>
                                        <a href="{{ route('estadisticas-encuesta', $encuesta) }}"
                                            class="rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs font-medium text-blue-700 hover:bg-gray-50 dark:border-blue-700 dark:bg-blue-800 dark:text-blue-400">
                                            Estadísticas
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    {{ !empty($search) ? 'No se encontraron encuestas para "' . $search . '"' : 'No hay encuestas registradas' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($encuestas->hasPages())
                <div class="border-t border-gray-100 px-6 py-4 dark:border-white/[0.05]">
                    {{ $encuestas->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirmar eliminación</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                ¿Está seguro de que desea eliminar esta encuesta? Esta acción no se puede deshacer.
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

            const setupToast = (toastId, closeButtonId) => {
                const toast = document.getElementById(toastId);
                const closeButton = document.getElementById(closeButtonId);

                if (!toast) return;

                const hideToast = () => toast.classList.add('hidden');
                const timer = setTimeout(hideToast, 5000);

                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        clearTimeout(timer);
                        hideToast();
                    });
                }
            };

            setupToast('successToast', 'closeSuccessToast');
            setupToast('errorToast', 'closeErrorToast');

            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');

            if (searchInput && searchButton) {
                const toggleSearchButton = () => {
                    const isEmpty = searchInput.value.trim() === '';
                    searchButton.disabled = isEmpty;
                    searchButton.classList.toggle('opacity-50', isEmpty);
                    searchButton.classList.toggle('cursor-not-allowed', isEmpty);
                };

                toggleSearchButton();
                searchInput.addEventListener('input', toggleSearchButton);
            }
        });
    </script>
@endsection
