@extends('layouts.encuestas')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $encuesta->nombre }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $encuesta->descripcion }}
            </p>
        </div>

        <!-- Información de la Encuesta -->
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6 dark:border-blue-900/30 dark:bg-blue-900/20">
            <h2 class="text-lg font-bold text-blue-900 dark:text-blue-200">Información Importante</h2>
            <ul class="mt-4 space-y-2 text-sm text-blue-800 dark:text-blue-300">
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                    <span>Esta encuesta contiene preguntas basadas en criterios DSM-5</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                    <span>El tiempo estimado de respuesta es de 5-10 minutos</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                    <span>Todas sus respuestas son confidenciales</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                    <span>Responda con sinceridad para obtener un análisis preciso</span>
                </li>
            </ul>
        </div>

        <!-- Formulario de Datos del Estudiante -->
        <form action="{{ route('guardar-datos-encuesta', $encuesta) }}" method="POST"
            class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            @csrf

            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Datos Personales</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Por favor complete la siguiente información</p>

            <div class="mt-6 space-y-4">
                <!-- Nombre Completo -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre Completo <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="nombre_estudiante" placeholder="Ingrese su nombre completo"
                        value="{{ old('nombre_estudiante') }}" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-blue-600 dark:focus:ring-blue-600/20">
                    @error('nombre_estudiante')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Edad -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Edad <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="edad_estudiante" placeholder="Ingrese su edad"
                        value="{{ old('edad_estudiante') }}" min="10" max="100" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-blue-600 dark:focus:ring-blue-600/20">
                    @error('edad_estudiante')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Género -->
                <div>
                    <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Género <span class="text-red-600">*</span>
                    </label>
                    <div class="space-y-3">
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-lg border-2 border-gray-300 p-4 transition-all hover:border-gray-400 dark:border-gray-700 dark:hover:border-gray-600">
                            <input type="radio" name="sexo_estudiante" value="M"
                                {{ old('sexo_estudiante') === 'M' ? 'checked' : '' }} class="h-4 w-4 cursor-pointer">
                            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">Masculino</span>
                        </label>
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-lg border-2 border-gray-300 p-4 transition-all hover:border-gray-400 dark:border-gray-700 dark:hover:border-gray-600">
                            <input type="radio" name="sexo_estudiante" value="F"
                                {{ old('sexo_estudiante') === 'F' ? 'checked' : '' }} class="h-4 w-4 cursor-pointer">
                            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">Femenino</span>
                        </label>
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-lg border-2 border-gray-300 p-4 transition-all hover:border-gray-400 dark:border-gray-700 dark:hover:border-gray-600">
                            <input type="radio" name="sexo_estudiante" value="O"
                                {{ old('sexo_estudiante') === 'O' ? 'checked' : '' }} class="h-4 w-4 cursor-pointer">
                            <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">Otro</span>
                        </label>
                    </div>
                    @error('sexo_estudiante')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-8 flex gap-4">
                <a href="/encuestas"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    ← Salir
                </a>
                <button type="submit"
                    class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/50 dark:bg-blue-700 dark:hover:bg-blue-800">
                    Comenzar Encuesta →
                </button>
            </div>
        </form>

        <!-- Preguntas Frecuentes -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Preguntas Frecuentes</h2>

            <div class="mt-6 space-y-4">
                <details class="group cursor-pointer">
                    <summary
                        class="flex cursor-pointer items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-white/[0.02]">
                        <span class="font-medium text-gray-800 dark:text-white">¿Qué es el TDA?</span>
                        <span class="text-2xl text-gray-600 dark:text-gray-400 group-open:rotate-180"
                            style="transform: rotate(0deg);">↓</span>
                    </summary>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        El Trastorno por Déficit de Atención (TDA) es una condición neurológica que afecta la capacidad de
                        concentración, atención y control de impulsos. Este cuestionario ayuda a identificar síntomas
                        asociados a esta condición según los criterios DSM-5.
                    </p>
                </details>

                <details class="group cursor-pointer">
                    <summary
                        class="flex cursor-pointer items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-white/[0.02]">
                        <span class="font-medium text-gray-800 dark:text-white">¿Es un diagnóstico definitivo?</span>
                        <span class="text-2xl text-gray-600 dark:text-gray-400 group-open:rotate-180"
                            style="transform: rotate(0deg);">↓</span>
                    </summary>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        No. Este cuestionario es un screening (detección inicial). Los resultados deben ser evaluados por un
                        profesional de salud mental para un diagnóstico definitivo.
                    </p>
                </details>

                <details class="group cursor-pointer">
                    <summary
                        class="flex cursor-pointer items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-white/[0.02]">
                        <span class="font-medium text-gray-800 dark:text-white">¿Cuánto tiempo tarda?</span>
                        <span class="text-2xl text-gray-600 dark:text-gray-400 group-open:rotate-180"
                            style="transform: rotate(0deg);">↓</span>
                    </summary>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        El tiempo estimado es de 5 a 10 minutos. Puede pausar en cualquier momento y continuar después.
                    </p>
                </details>

                <details class="group cursor-pointer">
                    <summary
                        class="flex cursor-pointer items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-white/[0.02]">
                        <span class="font-medium text-gray-800 dark:text-white">¿Mis datos son confidenciales?</span>
                        <span class="text-2xl text-gray-600 dark:text-gray-400 group-open:rotate-180"
                            style="transform: rotate(0deg);">↓</span>
                    </summary>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        Sí. Todos los datos son tratados de forma confidencial y segura según la política de privacidad.
                    </p>
                </details>
            </div>
        </div>
    </div>
@endsection
