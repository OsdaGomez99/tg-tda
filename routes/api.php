<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiEncuestaController;

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/store-session', [AuthController::class, 'storeSession'])->name('store-session');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('/is-auth', [AuthController::class, 'isAuth'])->name('is-auth');
});

// Encuestas y TDA Analysis routes
Route::prefix('encuestas')->group(function () {
    // Obtener encuestas disponibles
    Route::get('/', [ApiEncuestaController::class, 'index'])->name('encuestas.index');

    // Obtener una encuesta específica con sus preguntas
    Route::get('/{encuesta}', [ApiEncuestaController::class, 'show'])->name('encuestas.show');

    // Iniciar una nueva respuesta de encuesta
    Route::post('/{encuesta}/iniciar', [ApiEncuestaController::class, 'iniciar'])->name('encuestas.iniciar');

    // Obtener resultados de una encuesta
    Route::get('/{encuesta}/resultados', [ApiEncuestaController::class, 'obtenerResultados'])->name('encuestas.resultados');

    // Obtener estadísticas de una encuesta
    Route::get('/{encuesta}/estadisticas', [ApiEncuestaController::class, 'estadisticas'])->name('encuestas.estadisticas');
});

// Respuestas de encuesta
Route::prefix('respuestas')->group(function () {
    // Guardar una respuesta individual
    Route::post('/{resultado}', [ApiEncuestaController::class, 'guardarRespuesta'])->name('respuestas.store');

    // Guardar múltiples respuestas
    Route::post('/{resultado}/batch', [ApiEncuestaController::class, 'guardarRespuestas'])->name('respuestas.batch');

    // Finalizar encuesta y generar análisis
    Route::post('/{resultado}/finalizar', [ApiEncuestaController::class, 'finalizar'])->name('respuestas.finalizar');

    // Obtener resultado y análisis
    Route::get('/{resultado}', [ApiEncuestaController::class, 'obtenerResultado'])->name('respuestas.show');
});
