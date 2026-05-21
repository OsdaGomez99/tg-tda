@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $encuesta->nombre }}</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Cuestionario de detección de Trastorno por Déficit de Atención (TDA)
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-full w-0 bg-blue-500 transition-all duration-300" id="progressBar"></div>
                </div>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    <span id="currentQuestion">0</span> / <span id="totalQuestions">18</span>
                </span>
            </div>
        </div>

        <!-- Formulario Principal -->
        <form id="encuestaForm" class="space-y-4">
            @csrf
            <input type="hidden" id="resultadoId" value="{{ $resultado->id }}">

            <!-- Preguntas -->
            <div id="questionsContainer" class="space-y-6">
                <!-- Las preguntas se cargarán con JavaScript -->
            </div>

            <!-- Botones de Navegación -->
            <div class="flex gap-4">
                <button type="button" id="prevBtn"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-6 py-3 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                    style="display: none;">
                    ← Anterior
                </button>
                <button type="button" id="nextBtn"
                    class="flex-1 rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                    Siguiente →
                </button>
                <button type="button" id="submitBtn"
                    class="flex-1 rounded-lg bg-green-600 px-6 py-3 font-medium text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800"
                    style="display: none;">
                    Finalizar Encuesta
                </button>
            </div>
        </form>

        <!-- Indicadores de Respuesta -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Progreso de Respuestas</h3>
            <div class="grid grid-cols-9 gap-2" id="answeredIndicators">
                <!-- Indicadores se generarán con JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Finalización -->
    <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirmar Finalización</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                ¿Está seguro de que desea finalizar la encuesta? Se procederá a analizar sus respuestas.
            </p>
            <div class="mt-6 flex gap-3">
                <button type="button" id="cancelBtn"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Cancelar
                </button>
                <button type="button" id="confirmBtn"
                    class="flex-1 rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800">
                    Finalizar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Carga -->
    <div id="loadingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="rounded-2xl bg-white p-8 dark:bg-gray-900">
            <div class="mb-4 flex justify-center">
                <div
                    class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600 dark:border-gray-700 dark:border-t-blue-500">
                </div>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Analizando respuestas...</p>
        </div>
    </div>

    <script>
        const TOTAL_QUESTIONS = 18;
        const QUESTIONS_PER_PAGE = 3;
        let currentPage = 1;
        let responses = {};
        let allQuestions = [];
        const resultadoId = document.getElementById('resultadoId').value;

        // Opciones de respuesta
        const responseOptions = {
            0: 'Nunca o raramente',
            1: 'A veces',
            2: 'Con frecuencia',
            3: 'Muy frecuentemente'
        };

        const categoryColors = {
            'I': 'bg-amber-100 dark:bg-amber-900/30',
            'H': 'bg-blue-100 dark:bg-blue-900/30'
        };

        const categoryLabels = {
            'I': 'Inatención',
            'H': 'Hiperactividad/Impulsividad'
        };

        document.addEventListener('DOMContentLoaded', async () => {
            await loadQuestions();
            renderPage();
            setupEventListeners();
        });

        async function loadQuestions() {
            try {
                const response = await fetch(`/api/encuestas/{{ $encuesta->id }}`);
                const data = await response.json();
                allQuestions = data.preguntas;
            } catch (error) {
                console.error('Error cargando preguntas:', error);
                alert('Error al cargar las preguntas');
            }
        }

        function renderPage() {
            const startIdx = (currentPage - 1) * QUESTIONS_PER_PAGE;
            const endIdx = Math.min(startIdx + QUESTIONS_PER_PAGE, TOTAL_QUESTIONS);
            const pageQuestions = allQuestions.slice(startIdx, endIdx);

            const container = document.getElementById('questionsContainer');
            container.innerHTML = '';

            pageQuestions.forEach((question, idx) => {
                const questionNum = startIdx + idx + 1;
                const answered = responses[question.id] !== undefined;

                const html = `
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-4 flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                Pregunta ${questionNum} de ${TOTAL_QUESTIONS}
                            </h3>
                            <p class="mt-3 text-base font-medium text-gray-800 dark:text-white/90">
                                ${question.text}
                            </p>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                ${question.example}
                            </p>
                        </div>
                        <span class="ml-4 inline-flex items-center rounded-full ${categoryColors[question.category]} px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-200">
                            ${categoryLabels[question.category]}
                        </span>
                    </div>

                    <div class="space-y-2">
                        ${[0, 1, 2, 3].map(score => `
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border-2 p-4 transition-all ${
                                    responses[question.id] === score
                                        ? 'border-blue-500 bg-blue-50 dark:border-blue-600 dark:bg-blue-900/20'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                                }">
                                    <input
                                        type="radio"
                                        name="question_${question.id}"
                                        value="${score}"
                                        ${responses[question.id] === score ? 'checked' : ''}
                                        class="h-4 w-4 cursor-pointer"
                                        onchange="handleResponse(${question.id}, ${score})">
                                    <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        ${responseOptions[score]}
                                    </span>
                                </label>
                            `).join('')}
                    </div>
                </div>
            `;

                container.innerHTML += html;
            });

            updateUI();
        }

        function handleResponse(questionId, score) {
            responses[questionId] = score;
            saveResponse(questionId, score);
            renderPage();
        }

        async function saveResponse(questionId, score) {
            try {
                await fetch(`/api/respuestas/${resultadoId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pregunta_id: questionId,
                        puntuacion: score
                    })
                });
            } catch (error) {
                console.error('Error guardando respuesta:', error);
            }
        }

        function updateUI() {
            const totalAnswered = Object.keys(responses).length;
            const progress = (totalAnswered / TOTAL_QUESTIONS) * 100;

            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('currentQuestion').textContent = totalAnswered;

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            prevBtn.style.display = currentPage > 1 ? 'block' : 'none';

            const isLastPage = currentPage * QUESTIONS_PER_PAGE >= TOTAL_QUESTIONS;
            if (isLastPage) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = totalAnswered === TOTAL_QUESTIONS ? 'block' : 'none';
            } else {
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }

            // Actualizar indicadores
            updateIndicators();
        }

        function updateIndicators() {
            const container = document.getElementById('answeredIndicators');
            container.innerHTML = '';

            for (let i = 1; i <= TOTAL_QUESTIONS; i++) {
                const answered = responses[i] !== undefined;
                const indicator = document.createElement('div');
                indicator.className = `h-8 w-8 rounded-lg flex items-center justify-center text-xs font-bold cursor-pointer transition-all ${
                answered
                    ? 'bg-green-500 text-white'
                    : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
            }`;
                indicator.textContent = i;
                indicator.onclick = () => goToQuestion(i);
                container.appendChild(indicator);
            }
        }

        function goToQuestion(questionNumber) {
            currentPage = Math.ceil(questionNumber / QUESTIONS_PER_PAGE);
            renderPage();
        }

        function setupEventListeners() {
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage();
                }
            });

            document.getElementById('nextBtn').addEventListener('click', () => {
                if (currentPage * QUESTIONS_PER_PAGE < TOTAL_QUESTIONS) {
                    currentPage++;
                    renderPage();
                }
            });

            document.getElementById('submitBtn').addEventListener('click', () => {
                document.getElementById('confirmModal').classList.remove('hidden');
            });

            document.getElementById('cancelBtn').addEventListener('click', () => {
                document.getElementById('confirmModal').classList.add('hidden');
            });

            document.getElementById('confirmBtn').addEventListener('click', finalizeEncuesta);
        }

        async function finalizeEncuesta() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('loadingModal').classList.remove('hidden');

            try {
                const response = await fetch(`/api/respuestas/${resultadoId}/finalizar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Error al finalizar');

                const data = await response.json();

                // Redirigir a resultados
                setTimeout(() => {
                    window.location.href = `/respuestas/${resultadoId}/resultado`;
                }, 1500);
            } catch (error) {
                console.error('Error finalizando encuesta:', error);
                document.getElementById('loadingModal').classList.add('hidden');
                alert('Error al finalizar la encuesta. Por favor, intente nuevamente.');
            }
        }
    </script>

    <style>
        /* Animación de carga */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
@endsection
