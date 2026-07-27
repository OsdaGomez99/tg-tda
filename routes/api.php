<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiEncuestaController;


// Nota: sin 'auth' porque estos endpoints también los usa el flujo público por
// código de acceso. Para peticiones no autenticadas, el controlador exige que
// el código de acceso de la encuesta coincida (ver ApiEncuestaController).
Route::middleware(['web'])->group(function () {
    // Encuestas y TDA Analysis routes
    Route::prefix('encuestas')->name('api.encuestas.')->group(function () {
        // Obtener una encuesta específica con sus preguntas
        Route::get('/{encuesta}', [ApiEncuestaController::class, 'show'])->name('encuestas.show');

    });

    // Respuestas de encuesta
    Route::prefix('respuestas')->group(function () {
        // Guardar una respuesta individual
        Route::post('/{resultado}', [ApiEncuestaController::class, 'guardarRespuesta'])->name('respuestas.store');

        // Finalizar encuesta y generar análisis
        Route::post('/{resultado}/finalizar', [ApiEncuestaController::class, 'finalizar'])->name('respuestas.finalizar');

    });
});
