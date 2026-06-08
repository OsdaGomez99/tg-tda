@extends('layouts.app')

@section('content')

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch" x-data="preguntasSelector()">
        <!-- Panel Izquierdo: Formulario de Encuesta -->
        <div class="lg:col-span-1 flex flex-col h-full">
            <x-common.component-card title="Editar Encuesta" class="flex-1 h-full">
                <form action="{{ route('encuestas.update', $encuesta) }}" method="POST" @submit="agregarHiddens">
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
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Descripción
                        </label>
                        <textarea name="descripcion" placeholder="Ej: Cuestionario para evaluar posible TDA en estudiantes..." rows="6"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $encuesta->descripcion) }}</textarea>
                        @error('descripcion')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Usuario Responsable
                        </label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select name="usuario_id"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('usuario_id') border-red-500 @enderror"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                @change="isOptionSelected = true" required>
                                <option value="">Seleccionar usuario...</option>
                                @foreach (\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" @if (old('usuario_id') == $user->id || $encuesta->usuario_id == $user->id) selected @endif>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        @error('usuario_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ count($encuesta->preguntas) }} preguntas asignadas
                        </p>
                    </div>

                    <div id="hiddenPreguntaIds"></div>

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
        </div>

        <!-- Panel derecho para reasignar preguntas -->
        <div class="lg:col-span-2 flex flex-col">
            <x-common.component-card title="Reasignar Preguntas" class="flex-1">
                <div class="flex flex-col flex-1 space-y-4">
                    <!-- Controles de Filtro -->
                    <div class="flex flex-wrap gap-2 pb-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
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
                    <div class="space-y-2 overflow-y-auto" style="max-height: 500px">
                        <template x-for="pregunta in preguntasFiltradas" :key="pregunta.id">
                            <label
                                class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                                <input type="checkbox" :value="pregunta.id" x-model="seleccionadas"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 mt-0.5">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span x-text="pregunta.codigo"
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
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1" x-text="pregunta.nombre"></p>
                                    <template x-if="pregunta.descripcion">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                            <span x-text="pregunta.descripcion"></span>
                                        </p>
                                    </template>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
            </x-common.component-card>
        </div>
    </div>

    <script>
        function preguntasSelector() {
            return {
                filtro: 'todas',
                seleccionadas: @json($encuesta->preguntas->pluck('id')->toArray()),
                preguntas: {{ Js::from(
                    $preguntasDisponibles->map(fn($p) => [
                        'id'          => $p->id,
                        'codigo'      => $p->codigo,
                        'nombre'      => $p->nombre,
                        'tipo_tda'    => $p->tipo_tda,
                        'descripcion' => $p->descripcion,
                    ])->values()->toArray()
                ) }},

                get preguntasFiltradas() {
                    if (this.filtro === 'todas') return this.preguntas;
                    return this.preguntas.filter(p => p.tipo_tda === this.filtro);
                },

                agregarHiddens() {
                    const container = document.getElementById('hiddenPreguntaIds');
                    container.innerHTML = '';
                    this.seleccionadas.forEach(id => {
                        const input = document.createElement('input');
                        input.type  = 'hidden';
                        input.name  = 'pregunta_ids[]';
                        input.value = id;
                        container.appendChild(input);
                    });
                }
            }
        }
    </script>
@endsection
