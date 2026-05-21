@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Detalles de Respuestas</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $resultado->nombre_estudiante }} - Edad:
                {{ $resultado->edad_estudiante }} años</p>
        </div>

        <!-- Filtros -->
        <div class="flex flex-wrap gap-4">
            <button onclick="filterByCategory('all')"
                class="category-filter active rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
                data-category="all">
                Todas las Preguntas (18)
            </button>
            <button onclick="filterByCategory('I')"
                class="category-filter rounded-lg border border-amber-300 bg-white px-4 py-2 font-medium text-amber-700 hover:bg-amber-50 dark:border-amber-700 dark:bg-gray-800 dark:text-amber-300"
                data-category="I">
                Inatención (9)
            </button>
            <button onclick="filterByCategory('H')"
                class="category-filter rounded-lg border border-blue-300 bg-white px-4 py-2 font-medium text-blue-700 hover:bg-blue-50 dark:border-blue-700 dark:bg-gray-800 dark:text-blue-300"
                data-category="H">
                Hiperactividad (9)
            </button>
        </div>

        <!-- Tabla de Respuestas -->
        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <table class="w-full">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Pregunta</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Categoría
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Respuesta
                        </th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Puntuación
                        </th>
                    </tr>
                </thead>
                <tbody id="respuestasTable" class="divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Las respuestas se cargarán con JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Resumen de Estadísticas -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Análisis por Categoría -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Análisis por Categoría</h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Inatención</span>
                            <span class="text-sm font-bold text-amber-600 dark:text-amber-400"
                                id="inatencionPuntuacion">0/27</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div id="inatencionBar" class="h-full bg-amber-500 transition-all" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hiperactividad</span>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400"
                                id="hiperactividadPuntuacion">0/27</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div id="hiperactividadBar" class="h-full bg-blue-500 transition-all" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribución de Respuestas -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Distribución de Respuestas</h2>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Muy frecuentemente (3)</span>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-8 rounded-full bg-red-500"></div>
                            <span class="w-6 text-right text-sm font-bold text-gray-800 dark:text-white"
                                id="count3">0</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Con frecuencia (2)</span>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-8 rounded-full bg-orange-500"></div>
                            <span class="w-6 text-right text-sm font-bold text-gray-800 dark:text-white"
                                id="count2">0</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">A veces (1)</span>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-8 rounded-full bg-yellow-500"></div>
                            <span class="w-6 text-right text-sm font-bold text-gray-800 dark:text-white"
                                id="count1">0</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Nunca o raramente (0)</span>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-8 rounded-full bg-green-500"></div>
                            <span class="w-6 text-right text-sm font-bold text-gray-800 dark:text-white"
                                id="count0">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex gap-4">
            <a href="/respuestas/{{ $resultado->id }}/resultado"
                class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                ← Volver a Resultado
            </a>
            <button onclick="window.print()"
                class="flex-1 rounded-lg bg-gray-600 px-6 py-3 font-medium text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-800">
                📄 Imprimir Detalles
            </button>
        </div>
    </div>

    <script>
        const respuestasData = @json($respuestas);
        const analisisData = @json($analisis);
        let allRespuestas = [];
        let currentFilter = 'all';

        document.addEventListener('DOMContentLoaded', async () => {
            // Procesar datos
            allRespuestas = respuestasData.map((r, idx) => {
                return {
                    ...r,
                    number: idx + 1,
                    category: r.pregunta ? (r.pregunta.tipo_tda || 'I') : 'I'
                };
            });

            renderTable();
            updateStats();
        });

        function renderTable() {
            const tbody = document.getElementById('respuestasTable');
            tbody.innerHTML = '';

            const responseLabels = {
                0: 'Nunca o raramente',
                1: 'A veces',
                2: 'Con frecuencia',
                3: 'Muy frecuentemente'
            };

            const categoryLabels = {
                'I': 'Inatención',
                'H': 'Hiperactividad'
            };

            const categoryColors = {
                'I': 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
                'H': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200'
            };

            const respuestasToShow = currentFilter === 'all' ?
                allRespuestas :
                allRespuestas.filter(r => r.category === currentFilter);

            respuestasToShow.forEach((respuesta) => {
                const categoryColor = categoryColors[respuesta.category] || categoryColors['I'];
                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">${respuesta.pregunta_id}</td>
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-gray-800 dark:text-white">${respuesta.pregunta.nombre}</p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">${respuesta.pregunta.descripcion}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full ${categoryColor} px-3 py-1 text-xs font-medium">
                        ${categoryLabels[respuesta.category]}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">${responseLabels[respuesta.puntuacion]}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full ${
                        respuesta.puntuacion === 0 ? 'bg-green-100 text-green-700' :
                        respuesta.puntuacion === 1 ? 'bg-yellow-100 text-yellow-700' :
                        respuesta.puntuacion === 2 ? 'bg-orange-100 text-orange-700' :
                        'bg-red-100 text-red-700'
                    } text-sm font-bold">
                        ${respuesta.puntuacion}
                    </span>
                </td>
            `;
                tbody.appendChild(row);
            });
        }

        function filterByCategory(category) {
            currentFilter = category;

            // Actualizar botones
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'hover:bg-blue-700', 'text-white');
                btn.classList.add('border', 'bg-white', 'text-gray-700', 'dark:bg-gray-800');
            });

            event.target.classList.remove('border', 'bg-white', 'text-gray-700', 'dark:bg-gray-800');
            event.target.classList.add('active', 'bg-blue-600', 'hover:bg-blue-700', 'text-white');

            renderTable();
        }

        function updateStats() {
            if (!analisisData) return;

            // Actualizar barras de categoría
            const inatencionPercent = (analisisData.puntuacion_inatención / 27) * 100;
            const hiperactividadPercent = (analisisData.puntuacion_hiperactividad / 27) * 100;

            document.getElementById('inatencionBar').style.width = inatencionPercent + '%';
            document.getElementById('hiperactividadBar').style.width = hiperactividadPercent + '%';
            document.getElementById('inatencionPuntuacion').textContent = `${analisisData.puntuacion_inatención}/27`;
            document.getElementById('hiperactividadPuntuacion').textContent =
                `${analisisData.puntuacion_hiperactividad}/27`;

            // Contar distribución de respuestas
            const counts = {
                0: 0,
                1: 0,
                2: 0,
                3: 0
            };
            allRespuestas.forEach(r => {
                counts[r.puntuacion]++;
            });

            Object.keys(counts).forEach(key => {
                document.getElementById(`count${key}`).textContent = counts[key];
            });
        }
    </script>
@endsection
