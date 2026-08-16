@extends('layouts.app')

<?php
if (!function_exists('getResultadoBadgeClass')) {
    function getResultadoBadgeClass($resultado)
    {
        return match ($resultado) {
            'tda_combinado' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
            'tda_inatento' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
            'tda_hiperactivo' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200',
            'tda_posible' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
            'no_tda' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

if (!function_exists('getResultadoLabel')) {
    function getResultadoLabel($resultado)
    {
        return match ($resultado) {
            'tda_combinado' => 'TDA Combinado',
            'tda_inatento' => 'TDA Inatento',
            'tda_hiperactivo' => 'TDA Hiperactivo',
            'tda_posible' => 'Posible TDA',
            'no_tda' => 'Sin TDA',
            default => 'Desconocido',
        };
    }
}
?>

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $estudiante->nombre_estudiante }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $estudiante->edad_estudiante }} años,
                        @if ($estudiante->sexo_estudiante === 'M')
                            Masculino
                        @elseif($estudiante->sexo_estudiante === 'F')
                            Femenino
                        @else
                            Otro
                        @endif
                    </p>
                      <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $estudiante->documento_estudiante }},
                        {{ $estudiante->carrera->nombre ?? 'Sin carrera' }}
                    </p>
                </div>
                <a href="{{ route('estudiantes.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    ← Volver a Estudiantes
                </a>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Semestres participados</p>
                <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalSemestres }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Encuestas respondidas</p>
                <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $resultados->count() }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Último resultado</p>
                @if ($resultados->last()->analisisTda)
                    <span
                        class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ getResultadoBadgeClass($resultados->last()->analisisTda->resultado) }}">
                        {{ getResultadoLabel($resultados->last()->analisisTda->resultado) }}
                    </span>
                @else
                    <span
                        class="mt-2 inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-400">Pendiente</span>
                @endif
            </div>
        </div>

        <!-- Gráfica de Evolución -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Evolución de puntuaciones por semestre</h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Puntuación de inatención, hiperactividad y total en cada semestre en que el estudiante respondió la
                encuesta (máximo 27 / 27 / 54 puntos respectivamente).
            </p>
            <div class="mt-4" style="height: 320px;">
                <canvas id="chart-evolucion-estudiante"></canvas>
            </div>
        </div>

        <!-- Historial por Semestre -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-200 p-6 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Historial de participación</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Semestre</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Encuesta</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Resultado</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Puntuación total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Fecha</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach ($resultados as $resultado)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $resultado->semestre->nombre ?? 'Sin semestre' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $resultado->encuesta->nombre ?? '-' }}</td>
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
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    @if ($resultado->analisisTda)
                                        {{ \Carbon\Carbon::parse($resultado->analisisTda->created_at)->format('d/m/Y h:i A') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if ($resultado->analisisTda)
                                            <a href="{{ route('resultado-encuesta', $resultado) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-700/50 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                                Ver resultado
                                            </a>
                                            <a href="{{ route('detalles-encuesta', $resultado) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                Ver detalles
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('chart-evolucion-estudiante');
                const chartData = @json($chartData);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Inatención (máx. 27)',
                                data: chartData.inatencion,
                                borderColor: '#d97706',
                                backgroundColor: '#d97706',
                                tension: 0.3,
                                spanGaps: true,
                            },
                            {
                                label: 'Hiperactividad (máx. 27)',
                                data: chartData.hiperactividad,
                                borderColor: '#2563eb',
                                backgroundColor: '#2563eb',
                                tension: 0.3,
                                spanGaps: true,
                            },
                            {
                                label: 'Total (máx. 54)',
                                data: chartData.total,
                                borderColor: '#9333ea',
                                backgroundColor: '#9333ea',
                                tension: 0.3,
                                spanGaps: true,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 54,
                                ticks: { color: '#6b7280' },
                                grid: { color: 'rgba(107, 114, 128, 0.2)' },
                            },
                            x: {
                                ticks: { color: '#6b7280' },
                                grid: { color: 'rgba(107, 114, 128, 0.2)' },
                            },
                        },
                        plugins: {
                            legend: { labels: { color: '#6b7280' } },
                        },
                    },
                });
            });
        </script>
    @endpush
@endsection
