@extends('layouts.app')

@section('content')
    @if (session('error'))
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

    @php
        $totalRespondientes = $estadisticas['total_respondientes'] ?? 0;
        $completados = $estadisticas['resultados_completados'] ?? 0;
        $distribucion = $estadisticas['distribucion_resultados'] ?? [];
        $casosTda = ($distribucion['tda_combinado'] ?? 0) + ($distribucion['tda_inatento'] ?? 0) + ($distribucion['tda_hiperactivo'] ?? 0);
        $casosPosibles = $distribucion['tda_posible'] ?? 0;
        $pendientes = max($totalRespondientes - $completados, 0);

        $badgeClasses = [
            'tda_combinado'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
            'tda_inatento'    => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
            'tda_hiperactivo' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200',
            'tda_posible'     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
            'no_tda'          => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
        ];
        $badgeLabels = [
            'tda_combinado'   => 'TDA Combinado',
            'tda_inatento'    => 'TDA Inatento',
            'tda_hiperactivo' => 'TDA Hiperactivo',
            'tda_posible'     => 'Posible TDA',
            'no_tda'          => 'Sin TDA',
        ];
        $barColors = [
            'tda_combinado'   => 'bg-red-500',
            'tda_inatento'    => 'bg-amber-500',
            'tda_hiperactivo' => 'bg-orange-500',
            'tda_posible'     => 'bg-yellow-500',
            'no_tda'          => 'bg-green-500',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Resumen general</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Resumen de todas las encuestas y evaluaciones</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalRespondientes }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Estudiantes evaluados</p>
                </div>
            </div>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Encuestas Registradas</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $totalEncuestas }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Evaluados</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $totalRespondientes }}</p>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 dark:border-red-900/30 dark:bg-red-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Casos con TDA
                </p>
                <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $casosTda }}</p>
                <p class="mt-1 text-xs text-red-700 dark:text-red-300">Combinado, inatento o hiperactivo</p>
            </div>

            <div
                class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-300">Posible
                    TDA</p>
                <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $casosPosibles }}</p>
                <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-300">Bajo monitoreo preventivo</p>
            </div>
        </div>

        @if ($totalRespondientes > 0)
            <!-- Distribución y acciones rápidas -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Distribución de Resultados</h3>
                        <span class="text-xs text-gray-400">Total acumulado</span>
                    </div>
                    <div class="space-y-4">
                        @foreach (['no_tda', 'tda_posible', 'tda_inatento', 'tda_hiperactivo', 'tda_combinado'] as $tipo)
                            @php
                                $cantidad = $distribucion[$tipo] ?? 0;
                                $porcentaje = $totalRespondientes > 0 ? round(($cantidad / $totalRespondientes) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="mb-1 flex justify-between text-xs font-medium text-gray-600 dark:text-gray-400">
                                    <span>{{ $badgeLabels[$tipo] }}</span>
                                    <span>{{ $cantidad }} estudiantes ({{ $porcentaje }}%)</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="progress-bar h-2.5 rounded-full {{ $barColors[$tipo] }} transition-all duration-1000 ease-out"
                                        style="width: 0%" data-progress="{{ $porcentaje }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="mb-3 text-lg font-bold text-gray-800 dark:text-white">Accesos Rápidos</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('encuestas.create') }}">
                            <x-ui.button size="sm" variant="primary" class="w-full">Nueva Encuesta</x-ui.button>
                        </a>
                        <a href="{{ route('encuestas.index') }}">
                            <x-ui.button size="sm" variant="outline" class="w-full">Ver Encuestas</x-ui.button>
                        </a>
                        <a href="{{ route('preguntas.index') }}">
                            <x-ui.button size="sm" variant="outline" class="w-full">Banco de Preguntas</x-ui.button>
                        </a>
                        <a href="{{ route('usuarios.index') }}">
                            <x-ui.button size="sm" variant="outline" class="w-full">Usuarios</x-ui.button>
                        </a>
                    </div>
                    @if ($pendientes > 0)
                        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                            {{ $pendientes }} evaluación(es) respondida(s) sin analizar aún.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Promedios generales -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación Promedio - Inatención
                    </h3>
                    <p class="mt-4 text-3xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $estadisticas['promedio_inatencion'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">de 27 puntos</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="progress-bar h-full bg-amber-500 transition-all duration-1000 ease-out"
                            style="width: 0%" data-progress="{{ round((($estadisticas['promedio_inatencion'] ?? 0) / 27) * 100) }}"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación Promedio -
                        Hiperactividad</h3>
                    <p class="mt-4 text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $estadisticas['promedio_hiperactividad'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">de 27 puntos</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="progress-bar h-full bg-blue-500 transition-all duration-1000 ease-out"
                            style="width: 0%" data-progress="{{ round((($estadisticas['promedio_hiperactividad'] ?? 0) / 27) * 100) }}">
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación Promedio Total</h3>
                    <p class="mt-4 text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $estadisticas['promedio_total'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">de 54 puntos</p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="progress-bar h-full bg-purple-500 transition-all duration-1000 ease-out"
                            style="width: 0%" data-progress="{{ round((($estadisticas['promedio_total'] ?? 0) / 54) * 100) }}"></div>
                    </div>
                </div>
            </div>

            <!-- Datos demográficos -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Distribución por Género</h3>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Masculino</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white">
                                {{ $estadisticas['distribucion_genero']['M'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Femenino</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white">
                                {{ $estadisticas['distribucion_genero']['F'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Otro</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white">
                                {{ $estadisticas['distribucion_genero']['O'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Información Demográfica</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Edad Promedio</p>
                            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $estadisticas['edad_promedio'] ?? 0 }} años</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Evaluaciones Completadas
                            </p>
                            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $completados }} / {{ $totalRespondientes }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Casos prioritarios -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 p-6 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Casos Prioritarios</h3>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Estudiantes con resultado positivo,
                        ordenados por puntuación total</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Estudiante</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Encuesta</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Resultado</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Puntuación total</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Fecha de respuesta</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($prioritarios as $resultado)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                                        {{ $resultado->nombre_estudiante }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $resultado->encuesta->nombre ?? 'N/D' }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $badgeClasses[$resultado->analisisTda->resultado] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $badgeLabels[$resultado->analisisTda->resultado] ?? 'Desconocido' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-white">
                                        {{ $resultado->analisisTda->puntuacion_total }}/54</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ Carbon\Carbon::parse($resultado->analisisTda->created_at)->format('d/m/Y h:i A') }}</td>
                                    <td class="px-6 py-4">
                                        <a href="/respuestas/{{ $resultado->id }}/resultado"
                                            class="inline-flex items-center justify-center rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-700/50 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                            Ver resultado
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                        No hay casos prioritarios por el momento
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div
                class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                <p class="text-yellow-800 dark:text-yellow-200">
                    Aún no hay evaluaciones registradas en el sistema.
                </p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Doble rAF: asegura que el navegador pinte el ancho en 0% antes de animar al valor real
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    document.querySelectorAll('.progress-bar').forEach((bar) => {
                        bar.style.width = (bar.dataset.progress || 0) + '%';
                    });
                });
            });

            const errorToast = document.getElementById('errorToast');
            const closeErrorToast = document.getElementById('closeErrorToast');

            if (errorToast) {
                const hideToast = () => errorToast.classList.add('hidden');
                const timer = setTimeout(hideToast, 5000);

                if (closeErrorToast) {
                    closeErrorToast.addEventListener('click', () => {
                        clearTimeout(timer);
                        hideToast();
                    });
                }
            }
        });
    </script>
@endsection
