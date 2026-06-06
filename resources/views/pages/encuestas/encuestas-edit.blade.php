@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel Izquierdo: Formulario de Encuesta -->
        <div class="lg:col-span-1">
            <x-common.component-card title="Editar Encuesta">
                <form action="{{ route('encuestas.update', $encuesta) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nombre de la Encuesta
                        </label>
                        <input type="text" name="nombre" placeholder="Ej: Screening TDA 2026..."
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('nombre') border-red-500 @enderror"
                            value="{{ old('nombre', $encuesta->nombre) }}" required />
                        @error('nombre')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ count($encuesta->preguntas) }} preguntas asignadas
                        </p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Guardar Cambios
                        </button>
                        <a href="{{ route('encuestas.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            Cancelar
                        </a>
                    </div>
                </form>
            </x-common.component-card>

            <div class="mt-4">
                <x-common.component-card title="Acciones">
                    <div class="space-y-2">
                        <form action="{{ route('encuestas.destroy', $encuesta) }}" method="POST" class="inline"
                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta encuesta?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                Eliminar Encuesta
                            </button>
                        </form>
                    </div>
                </x-common.component-card>
            </div>
        </div>

        <!-- Panel Derecho: Preguntas Asignadas -->
        <div class="lg:col-span-2">
            <x-common.component-card title="Preguntas Asignadas ({{ $encuesta->preguntas->count() }})">
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($encuesta->preguntas as $index => $pregunta)
                        <div
                            class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <span
                                class="text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                        #{{ $pregunta->id }}
                                    </span>
                                    @if ($pregunta->tipo_tda === 'I')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                            Inatención
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Hiperactividad
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $pregunta->nombre }}</p>
                                @if ($pregunta->descripcion)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">Ej:
                                        {{ $pregunta->descripcion }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No hay preguntas asignadas a esta encuesta</p>
                        </div>
                    @endforelse
                </div>
            </x-common.component-card>

            <!-- Panel para reasignar preguntas -->
            <div class="mt-4">
                <x-common.component-card title="Reasignar Preguntas">
                    <form action="{{ route('encuestas.asignar-preguntas-store', $encuesta) }}" method="POST">
                        @csrf

                        <div x-data="preguntasSelector()" class="space-y-4">
                            <!-- Controles de Filtro -->
                            <div class="flex flex-wrap gap-2 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <button type="button" @click="filtro = 'todas'"
                                    :class="{ 'bg-blue-600 text-white': filtro === 'todas', 'bg-gray-200 dark:bg-gray-700': filtro !== 'todas' }"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                    Todas
                                </button>
                                <button type="button" @click="filtro = 'I'"
                                    :class="{ 'bg-amber-600 text-white': filtro === 'I', 'bg-gray-200 dark:bg-gray-700': filtro !== 'I' }"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                    Inatención
                                </button>
                                <button type="button" @click="filtro = 'H'"
                                    :class="{ 'bg-blue-600 text-white': filtro === 'H', 'bg-gray-200 dark:bg-gray-700': filtro !== 'H' }"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                    Hiperactividad
                                </button>
                                <div class="ml-auto flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                        <span x-text="seleccionadas.length"></span> / <span
                                            x-text="preguntas.length"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Lista de Preguntas -->
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-for="pregunta in preguntasFiltradas" :key="pregunta.id">
                                    <label
                                        class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                                        <input type="checkbox" :value="pregunta.id" x-model="seleccionadas"
                                            name="pregunta_ids"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 mt-0.5">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span x-text="pregunta.id"
                                                    class="text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded"></span>
                                                <span
                                                    :class="{
                                                        'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200': pregunta
                                                            .tipo_tda === 'I',
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': pregunta
                                                            .tipo_tda === 'H'
                                                    }"
                                                    class="text-xs font-semibold px-2 py-0.5 rounded">
                                                    <span
                                                        x-text="pregunta.tipo_tda === 'I' ? 'Inatención' : 'Hiperactividad'"></span>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1"
                                                x-text="pregunta.nombre"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-green-600 px-6 py-2.5 text-center text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                    Actualizar Preguntas
                                </button>
                            </div>
                        </div>
                    </form>
                </x-common.component-card>
            </div>
        </div>
    </div>

    <script>
        function preguntasSelector() {
            return {
                filtro: 'todas',
                seleccionadas: @json($encuesta->preguntas->pluck('id')->toArray()),
                preguntas: {{ Js::from(
                    $preguntasDisponibles->map(
                            fn($p) => [
                                'id' => $p->id,
                                'nombre' => $p->nombre,
                                'tipo_tda' => $p->tipo_tda,
                                'descripcion' => $p->descripcion,
                            ],
                        )->values()->toArray(),
                ) }},

                get preguntasFiltradas() {
                    if (this.filtro === 'todas') {
                        return this.preguntas;
                    }
                    return this.preguntas.filter(p => p.tipo_tda === this.filtro);
                }
            }
        }
    </script>
@endsection
