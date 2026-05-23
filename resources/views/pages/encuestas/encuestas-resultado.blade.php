@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        @if ($analisis)
            <!-- Resumen General -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Resultado Principal -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] md:col-span-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Resultado del Análisis</h1>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $resultado->nombre_estudiante }},
                                {{ $resultado->edad_estudiante }} años</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-bold {{ getResultadoBadgeClass($analisis->resultado) }}">
                                {{ getResultadoLabel($analisis->resultado) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Puntuación Inatención -->
                <div class="rounded-2xl border p-6 dark-card-amber" style="border-color: #fcd34d; background-color: #fffbeb;">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold" style="color: #78350f;">Inatención</h3>
                        <span class="text-lg font-bold" style="color: #d97706;">{{ $analisis->porcentaje_inatención }}%</span>
                    </div>
                    <div class="mb-3 h-3 overflow-hidden rounded-full" style="background-color: #fde68a;">
                        <div class="h-full transition-all"
                            style="width: {{ $analisis->porcentaje_inatención }}%; background-color: #d97706;"></div>
                    </div>
                    <p class="text-sm" style="color: #92400e;">
                        Puntuación: <span class="font-bold">{{ $analisis->puntuacion_inatención }}/27</span>
                    </p>
                    <p class="mt-2 text-xs" style="color: #b45309;">
                        Síntomas significativos: <span class="font-bold">{{ $analisis->sintomas_inatención }}/9</span>
                    </p>
                </div>

                <!-- Puntuación Hiperactividad -->
                <div class="rounded-2xl border p-6 dark-card-blue" style="border-color: #93c5fd; background-color: #eff6ff;">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold" style="color: #1e3a8a;">Hiperactividad/Impulsividad</h3>
                        <span class="text-lg font-bold" style="color: #2563eb;">{{ $analisis->porcentaje_hiperactividad }}%</span>
                    </div>
                    <div class="mb-3 h-3 overflow-hidden rounded-full" style="background-color: #bfdbfe;">
                        <div class="h-full transition-all"
                            style="width: {{ $analisis->porcentaje_hiperactividad }}%; background-color: #2563eb;"></div>
                    </div>
                    <p class="text-sm" style="color: #1e40af;">
                        Puntuación: <span class="font-bold">{{ $analisis->puntuacion_hiperactividad }}/27</span>
                    </p>
                    <p class="mt-2 text-xs" style="color: #1d4ed8;">
                        Síntomas significativos: <span class="font-bold">{{ $analisis->sintomas_hiperactividad }}/9</span>
                    </p>
                </div>

                <!-- Puntuación Total -->
                <div class="rounded-2xl border p-6 dark-card-purple" style="border-color: #d8b4fe; background-color: #faf5ff;">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold" style="color: #4c1d95;">Puntuación Total</h3>
                        <span class="text-lg font-bold" style="color: #a855f7;">{{ round(($analisis->puntuacion_total / 54) * 100) }}%</span>
                    </div>
                    <div class="mb-3 h-3 overflow-hidden rounded-full" style="background-color: #e9d5ff;">
                        <div class="h-full transition-all"
                            style="width: {{ round(($analisis->puntuacion_total / 54) * 100) }}%; background-color: #a855f7;"></div>
                    </div>
                    <p class="text-sm" style="color: #6b21a8;">
                        <span class="font-bold">{{ $analisis->puntuacion_total }}/54</span>
                    </p>
                </div>
            </div>

            <!-- Descripción Detallada -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Análisis Detallado</h2>
                <div class="mt-4 space-y-3">
                    <div
                        class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 dark:border-blue-600 dark:bg-blue-900/20">
                        <p class="text-sm text-blue-900 dark:text-blue-200">
                            <span class="font-bold">Resultado:</span> {{ $analisis->getResultadoDescripcion() }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $analisis->descripcion }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recomendaciones -->
            <div class="rounded-2xl border border-green-200 bg-green-50 p-6 dark:border-green-900/30 dark:bg-green-900/20">
                <h2 class="text-lg font-bold text-green-900 dark:text-green-200">Recomendaciones</h2>
                <ul class="mt-4 space-y-2 text-sm text-green-800 dark:text-green-300">
                    @if ($analisis->resultado !== 'no_tda')
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-600 text-black">✓</span>
                            <span>Se recomienda evaluación profesional con especialista en psicología o neurología.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-600 text-black">✓</span>
                            <span>Considere pruebas diagnósticas adicionales para confirmación clínica.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-600 text-black">✓</span>
                            <span>Este cuestionario es un screening inicial y no reemplaza diagnóstico profesional.</span>
                        </li>
                    @else
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-600 text-black">✓</span>
                            <span>No se detectan síntomas clínicamente significativos de TDA.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-600 text-black">✓</span>
                            <span>Mantenga un seguimiento regular de su salud mental.</span>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Botones de Acción -->
            <div class="flex gap-4">
                <a href="/encuestas"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    ← Volver a Encuestas
                </a>
                <button onclick="window.print()"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    📄 Imprimir Resultado
                </button>
                <a href="/respuestas/{{ $resultado->id }}/detalles"
                    class="flex-1 rounded-lg bg-blue-600 px-6 py-3 text-center font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                    Ver Detalles
                </a>
            </div>
        @else
            <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                <p class="text-yellow-800 dark:text-yellow-200">
                    La encuesta aún no ha sido finalizada. Por favor, complete todas las preguntas.
                </p>
                <a href="/respuestas/{{ $resultado->id }}/responder"
                    class="mt-4 inline-block rounded-lg bg-blue-600 px-6 py-3 text-center font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                    Continuar Respondiendo
                </a>
            </div>
        @endif
    </div>

    @push('styles')
        <style media="print">
            body * {
                visibility: hidden;
            }

            .content-to-print,
            .content-to-print * {
                visibility: visible;
            }

            .print-hidden {
                display: none !important;
            }

            .dark .dark-card-amber {
                background-color: rgba(120, 53, 15, 0.2) !important;
                border-color: rgba(180, 83, 9, 0.3) !important;
            }
            .dark .dark-card-blue {
                background-color: rgba(30, 58, 138, 0.2) !important;
                border-color: rgba(37, 99, 235, 0.3) !important;
            }
            .dark .dark-card-purple {
                background-color: rgba(76, 29, 149, 0.2) !important;
                border-color: rgba(168, 85, 247, 0.3) !important;
            }
        </style>
    @endpush
@endsection

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
