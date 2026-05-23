@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $encuesta->nombre }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Resultados Consolidados</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $estadisticas['total_respondientes'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Respondientes</p>
                </div>
            </div>
        </div>

        @if ($estadisticas && count($estadisticas) > 0)
            <!-- Distribución de Resultados -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-2xl border border-red-200 bg-red-50 p-6 dark:border-red-900/30 dark:bg-red-900/20">
                    <p class="text-sm font-medium text-red-900 dark:text-red-200">TDA Combinado</p>
                    <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
                        {{ $estadisticas['distribucion_resultados']['tda_combinado'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-red-700 dark:text-red-300">
                        {{ round(($estadisticas['distribucion_resultados']['tda_combinado'] / ($estadisticas['total_respondientes'] ?: 1)) * 100) }}%
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-6 dark:border-amber-900/30 dark:bg-amber-900/20">
                    <p class="text-sm font-medium text-amber-900 dark:text-amber-200">TDA Inatento</p>
                    <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $estadisticas['distribucion_resultados']['tda_inatento'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-amber-700 dark:text-amber-300">
                        {{ round(($estadisticas['distribucion_resultados']['tda_inatento'] / ($estadisticas['total_respondientes'] ?: 1)) * 100) }}%
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-orange-200 bg-orange-50 p-6 dark:border-orange-900/30 dark:bg-orange-900/20">
                    <p class="text-sm font-medium text-orange-900 dark:text-orange-200">TDA Hiperactivo</p>
                    <p class="mt-2 text-3xl font-bold text-orange-600 dark:text-orange-400">
                        {{ $estadisticas['distribucion_resultados']['tda_hiperactivo'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-orange-700 dark:text-orange-300">
                        {{ round(($estadisticas['distribucion_resultados']['tda_hiperactivo'] / ($estadisticas['total_respondientes'] ?: 1)) * 100) }}%
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                    <p class="text-sm font-medium text-yellow-900 dark:text-yellow-200">Posible TDA</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ $estadisticas['distribucion_resultados']['tda_possible'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-yellow-700 dark:text-yellow-300">
                        {{ round(($estadisticas['distribucion_resultados']['tda_possible'] / ($estadisticas['total_respondientes'] ?: 1)) * 100) }}%
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-green-200 bg-green-50 p-6 dark:border-green-900/30 dark:bg-green-900/20">
                    <p class="text-sm font-medium text-green-900 dark:text-green-200">Sin TDA</p>
                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ $estadisticas['distribucion_resultados']['no_tda'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-green-700 dark:text-green-300">
                        {{ round(($estadisticas['distribucion_resultados']['no_tda'] / ($estadisticas['total_respondientes'] ?: 1)) * 100) }}%
                    </p>
                </div>
            </div>

            <!-- Análisis Promedio -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación Promedio - Inatención</h3>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                            {{ $estadisticas['promedio_inatención'] ?? 0 }}</p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">de 27 puntos</p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-amber-500"
                                style="width: {{ round(($estadisticas['promedio_inatención'] / 27) * 100) }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación Promedio - Hiperactividad
                    </h3>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $estadisticas['promedio_hiperactividad'] ?? 0 }}</p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">de 27 puntos</p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-blue-500"
                                style="width: {{ round(($estadisticas['promedio_hiperactividad'] / 27) * 100) }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación Promedio Total</h3>
                    <div class="mt-4">
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $estadisticas['promedio_total'] ?? 0 }}</p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">de 54 puntos</p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-purple-500"
                                style="width: {{ round(($estadisticas['promedio_total'] / 54) * 100) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos Demográficos -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Distribución por Género</h3>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Masculino</span>
                            <div class="flex items-center gap-2">
                                <div class="h-2 flex-1 rounded-full bg-blue-500" style="width: 100px;"></div>
                                <span
                                    class="w-8 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $estadisticas['distribucion_genero']['M'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Femenino</span>
                            <div class="flex items-center gap-2">
                                <div class="h-2 flex-1 rounded-full bg-pink-500" style="width: 100px;"></div>
                                <span
                                    class="w-8 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $estadisticas['distribucion_genero']['F'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Otro</span>
                            <div class="flex items-center gap-2">
                                <div class="h-2 flex-1 rounded-full bg-gray-500" style="width: 100px;"></div>
                                <span
                                    class="w-8 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $estadisticas['distribucion_genero']['O'] ?? 0 }}</span>
                            </div>
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
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Total de Respondientes</p>
                            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $estadisticas['total_respondientes'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Respondientes -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 p-6 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Listado de Respondientes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Nombre</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Edad
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Género</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Resultado</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Puntuación Total</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse($resultados as $resultado)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                                        {{ $resultado->nombre_estudiante }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $resultado->edad_estudiante }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($resultado->sexo_estudiante === 'M')
                                            Masculino
                                        @elseif($resultado->sexo_estudiante === 'F')
                                            Femenino
                                        @else
                                            Otro
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($resultado->analisisTda)
                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ getResultadoBadgeClass($resultado->analisisTda->resultado) }}">
                                                {{ getResultadoLabel($resultado->analisisTda->resultado) }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-400">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($resultado->analisisTda)
                                            <span
                                                class="text-sm font-bold text-gray-800 dark:text-white">{{ $resultado->analisisTda->puntuacion_total }}/54</span>
                                        @else
                                            <span class="text-sm text-gray-600 dark:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="/respuestas/{{ $resultado->id }}/resultado"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Ver →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                        No hay respondientes aún
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
                    No hay resultados disponibles para esta encuesta aún.
                </p>
            </div>
        @endif

        <!-- Botones de Acción -->
        <div class="flex gap-4">
            <a href="/encuestas"
                class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                ← Volver a Encuestas
            </a>
            <button onclick="window.print()"
                class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                📊 Exportar Estadísticas
            </button>
        </div>
    </div>

    <?php
    function getResultadoBadgeClass($resultado)
    {
        return match ($resultado) {
            'tda_combinado' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
            'tda_inatento' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
            'tda_hiperactivo' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200',
            'tda_possible' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
            'no_tda' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    function getResultadoLabel($resultado)
    {
        return match ($resultado) {
            'tda_combinado' => 'TDA Combinado',
            'tda_inatento' => 'TDA Inatento',
            'tda_hiperactivo' => 'TDA Hiperactivo',
            'tda_possible' => 'Posible TDA',
            'no_tda' => 'Sin TDA',
            default => 'Desconocido',
        };
    }
    ?>
@endsection
