@php
    $isPublicRoute = request()->routeIs('encuestas.public.resultado');
    $layout = $isPublicRoute ? 'layouts.encuestas' : 'layouts.app';
@endphp

@extends($layout)
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
                            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $resultado->carrera->nombre }}</p>
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
                <div class="rounded-2xl border p-6 border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-300">Inatención</h3>
                        <span
                            class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $analisis->porcentaje_inatención }}%</span>
                    </div>
                    <div class="mb-3 h-3 overflow-hidden rounded-full bg-amber-200 dark:bg-amber-800">
                        <div class="h-full transition-all bg-amber-500 dark:bg-amber-400"
                            style="width: {{ $analisis->porcentaje_inatención }}%;"></div>
                    </div>
                    <p class="text-sm text-amber-800 dark:text-amber-300">
                        Puntuación: <span class="font-bold">{{ $analisis->puntuacion_inatención }}/27</span>
                    </p>
                    <p class="mt-2 text-xs text-amber-700 dark:text-amber-400">
                        Síntomas significativos: <span class="font-bold">{{ $analisis->sintomas_inatención }}/9</span>
                    </p>
                </div>

                <!-- Puntuación Hiperactividad -->
                <div class="rounded-2xl border p-6 border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300">Hiperactividad/Impulsividad</h3>
                        <span
                            class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $analisis->porcentaje_hiperactividad }}%</span>
                    </div>
                    <div class="mb-3 h-3 overflow-hidden rounded-full bg-blue-200 dark:bg-blue-800">
                        <div class="h-full transition-all bg-blue-500 dark:bg-blue-400"
                            style="width: {{ $analisis->porcentaje_hiperactividad }}%;"></div>
                    </div>
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        Puntuación: <span class="font-bold">{{ $analisis->puntuacion_hiperactividad }}/27</span>
                    </p>
                    <p class="mt-2 text-xs text-blue-700 dark:text-blue-400">
                        Síntomas significativos: <span class="font-bold">{{ $analisis->sintomas_hiperactividad }}/9</span>
                    </p>
                </div>

                <!-- Puntuación Total -->
                <div
                    class="rounded-2xl border p-6 border-purple-300 bg-purple-50 dark:border-purple-700 dark:bg-purple-900/20">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-purple-900 dark:text-purple-300">Puntuación Total</h3>
                        <span
                            class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ round(($analisis->puntuacion_total / 54) * 100) }}%</span>
                    </div>
                    <div class="mb-3 h-3 overflow-hidden rounded-full bg-purple-200 dark:bg-purple-800">
                        <div class="h-full transition-all bg-purple-500 dark:bg-purple-400"
                            style="width: {{ round(($analisis->puntuacion_total / 54) * 100) }}%;"></div>
                    </div>
                    <p class="text-sm text-purple-800 dark:text-purple-300">
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
            <div
                class="rounded-2xl border border-emerald-300 bg-emerald-50 p-6 dark:border-emerald-800 dark:bg-emerald-900/20">

                <div class="flex items-center space-x-2 mb-4">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-bold text-emerald-950 dark:text-emerald-100">Orientación y Pautas de
                        Organización Académica</h3>
                </div>

                <div class="space-y-4 text-sm leading-relaxed text-emerald-800 dark:text-emerald-200">

                    {{-- CASO 1: SIN TDA --}}
                    @if ($analisis->resultado == 'no_tda')
                        <p>Tus hábitos de organización e indicadores atencionales se encuentran dentro de los parámetros
                            típicos. Para mantener tu rendimiento académico en la UNEG, te sugerimos:</p>
                        <ul class="list-disc pl-6 space-y-2 text-emerald-950 dark:text-emerald-100">
                            <li><strong>Planificación Semanal:</strong> Dedica 15 minutos al inicio de la semana para
                                calendarizar tus entregas.</li>
                            <li><strong>Descansos Activos:</strong> Implementa pausas de 5 minutos por cada hora de estudio
                                continuo para evitar la fatiga mental.</li>
                        </ul>

                        {{-- CASO 2: TDA INATENCIÓN --}}
                    @elseif($analisis->resultado == 'tda_inatento')
                        <p>Se identificaron indicadores significativos en la dimensión de desatención. El principal desafío
                            es el enfoque prolongado y la gestión del tiempo. Te sugerimos aplicar:</p>
                        <ul class="list-disc pl-5 space-y-2 text-emerald-950 dark:text-emerald-100">
                            <li><strong>Método Pomodoro (25/5):</strong> Estudia en bloques cerrados de 25 minutos con
                                temporizador y descansa 5 minutos. Evita las jornadas maratónicas.</li>
                            <li><strong>Control Estricto de Estímulos:</strong> Retira el teléfono de tu campo visual y usa
                                extensiones en el navegador para bloquear redes sociales mientras estudias.</li>
                            <li><strong>Segmentación de Tareas:</strong> Divide los proyectos complejos en micro-tareas
                                diarias de 15 minutos.</li>
                        </ul>

                        {{-- CASO 3: TDA HIPERACTIVIDAD --}}
                    @elseif($analisis->resultado == 'tda_hiperactivo')
                        <p>Se identificaron indicadores significativos en la dimensión de hiperactividad e impulsividad. Tu
                            perfil requiere canalizar la energía física y mitigar la procrastinación por aburrimiento. Te
                            sugerimos:</p>
                        <ul class="list-disc pl-5 space-y-2 text-emerald-950 dark:text-emerald-100">
                            <li><strong>Estudio en Movimiento / Cambios de Entorno:</strong> Incorpora el uso de escritorios
                                de pie si es posible, o alterna tus lugares de estudio.</li>
                            <li><strong>Canalización Física Previa:</strong> Realiza una actividad física ligera o caminata
                                corta de 10 minutos antes de sentarte a procesar lecturas complejas.</li>
                            <li><strong>Técnicas de Estudio Activo:</strong> Evita la lectura pasiva. Utiliza mapas
                                mentales, explica la materia en voz alta o escribe notas breves.</li>
                        </ul>

                        {{-- CASO 4: TDA COMBINADO --}}
                    @elseif($analisis->resultado == 'tda_combinado')
                        <p>Se identificaron indicadores concurrentes tanto en desatención como en hiperactividad. Es el
                            perfil que requiere mayor estructura externa para evitar la sobrecarga cognitiva. Te sugerimos:
                        </p>
                        <ul class="list-disc pl-5 space-y-2 text-emerald-950 dark:text-emerald-100">
                            <li><strong>Listas de Tareas Prioritarias (Regla de 3):</strong> Anota solo 3 actividades
                                cruciales al inicio del día.</li>
                            <li><strong>Asistentes Visuales y Recordatorios:</strong> Utiliza alarmas sonoras o tableros
                                visuales físicos en tu área de estudio.</li>
                            <li><strong>Entornos Libres de Interrupciones:</strong> Busca espacios de alta estructura para
                                tus horas de mayor exigencia académica.</li>
                        </ul>

                        {{-- CASO 5: POSIBLE TDA --}}
                    @elseif($analisis->resultado == 'tda_posible')
                        <p>Se identificaron algunos indicadores de dispersión o dificultades atencionales aisladas que
                            pueden estar afectando tu rendimiento académico diario. Te sugerimos:</p>
                        <ul class="list-disc pl-5 space-y-2 text-emerald-950 dark:text-emerald-100">
                            <li><strong>Uso de Agendas o Recordatorios Digitales:</strong> Apóyate en herramientas como
                                Google Calendar o Notion.</li>
                            <li><strong>Listas de Tareas Diarias:</strong> Anota un máximo de 3 actividades clave para
                                mitigar la procrastinación.</li>
                            <li><strong>Auto-monitoreo de Distracciones:</strong> Identifica y reduce tus mayores
                                distractores durante los bloques de estudio.</li>
                        </ul>
                    @endif

                    {{-- Alerta Institucional --}}
                    @if ($analisis->resultado != 'no_tda')
                        <div
                            class="mt-4 p-3 rounded-lg border border-emerald-300 bg-white/60 dark:border-emerald-700 dark:bg-emerald-900/40">
                            <p class="text-xs text-emerald-800 dark:text-emerald-200">
                                <strong>💡 Nota de Acompañamiento:</strong> Este reporte ha sido generado con fines de
                                cribado y orientación psicoeducativa. El personal del Área de Bienestar Estudiantil de la
                                UNEG tiene acceso confidencial a estos resultados para ofrecerte estrategias de apoyo
                                personalizadas si así lo requieres.
                            </p>
                        </div>
                    @else
                        <div
                            class="mt-4 pt-4 text-xs italic border-t border-emerald-300/60 text-emerald-800/80 dark:border-emerald-700/60 dark:text-emerald-200/80">
                            * Nota: Este reporte es estrictamente confidencial y forma parte del programa de detección
                            temprana de Bienestar Estudiantil.
                        </div>
                    @endif

                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex gap-4">
                @if (request()->routeIs('encuestas.public.resultado'))
                    <button
                        onclick="window.close(); setTimeout(() => alert('Ya puede cerrar esta pestaña.'), 100)"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        ← Salir
                    </button>
                @else
                    <a href="{{ route('estadisticas-encuesta', $resultado->encuesta->id) }}"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        ← Volver a Estadísticas
                    </a>
                @endif
                <a href="{{ request()->routeIs('encuestas.public.resultado')
                    ? route('encuestas.public.resultado.pdf', [
                        'codigo_acceso' => $resultado->encuesta->codigo_acceso,
                        'resultado' => urlencode(base64_encode(encrypt($resultado->id))),
                    ])
                    : route('resultado-encuesta.pdf', $resultado) }}"
                    target="_blank"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    📄 Descargar resultados en PDF
                </a>
                <a href="{{ request()->routeIs('encuestas.public.resultado')
                    ? route('encuestas.public.detalles', [
                        'codigo_acceso' => $resultado->encuesta->codigo_acceso,
                        'resultado' => urlencode(base64_encode(encrypt($resultado->id))),
                    ])
                    : route('detalles-encuesta', $resultado) }}"
                    class="flex-1 rounded-lg bg-blue-600 px-6 py-3 text-center font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                    Ver Detalles
                </a>
            </div>
        @else
            <div
                class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                @if (request()->routeIs('encuestas.public.resultado'))
                    <p class="text-yellow-800 dark:text-yellow-200">
                        La encuesta aún no ha sido finalizada. Por favor, complete todas las preguntas.
                    </p>
                    <a href="{{ Auth::check()
                        ? route('responder-encuesta', $resultado)
                        : route('encuestas.public.responder', [
                            'codigo_acceso' => $resultado->encuesta->codigo_acceso,
                            'resultado' => str_replace(['+', '/', '='], ['-', '_', ''], encrypt($resultado->id)),
                        ]) }}"
                        class="mt-4 inline-block rounded-lg bg-blue-600 px-6 py-3 text-center font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                        Continuar Respondiendo
                    </a>
                @else
                    <p class="text-yellow-800 dark:text-yellow-200">
                        La encuesta aún no ha sido finalizada por el estudiante. Por favor, espere sus resultados.
                    </p>
                    <a href="{{ route('estadisticas-encuesta', $resultado->encuesta->id) }}"
                        class="mt-4 inline-block rounded-lg bg-blue-600 px-6 py-3 text-center font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                        ← Volver a Estadísticas
                    </a>
                @endif
            </div>
        @endif
    </div>

    <script>
        function closeWindow() {
            window.close();
        }
    </script>

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
        'tda_posible' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
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
        'tda_posible' => 'Posible TDA',
        'no_tda' => 'Sin TDA',
        default => 'Desconocido',
    };
}
?>
