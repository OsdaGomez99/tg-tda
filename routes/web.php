<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\ApiEncuestaController;
use App\Http\Controllers\EncuestaWebController;
use App\Http\Controllers\ResumenController;
use Illuminate\Support\Facades\Route;

// ===== RUTAS PÚBLICAS (SIN AUTENTICACIÓN) =====

// Páginas de autenticación
Route::get('/login', function () {
    return view('pages.auth.signin', ['title' => 'Iniciar Sesión']);
})->name('login');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Registrarse']);
})->name('signup');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/register', [AuthController::class, 'register'])->name('register');

// Página de error 404
Route::fallback(function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
});


// ===== RUTAS PÚBLICAS DE ENCUESTA POR CÓDIGO DE ACCESO =====
Route::get('/encuestas/acceso/{codigo_acceso}/iniciar', [EncuestaWebController::class, 'iniciarPublic'])->name('encuestas.public.iniciar');
Route::post('/encuestas/acceso/{codigo_acceso}/guardar-datos', [EncuestaWebController::class, 'storePublic'])->name('encuestas.public.guardar-datos');
Route::get('/encuestas/acceso/{codigo_acceso}/responder/{resultado}', [EncuestaWebController::class, 'responderPublic'])->name('encuestas.public.responder');
Route::get('/encuestas/acceso/{codigo_acceso}/resultado/{resultado}', [EncuestaWebController::class, 'resultadoPublic'])->name('encuestas.public.resultado');
Route::get('/encuestas/acceso/{codigo_acceso}/resultado/{resultado}/pdf', [EncuestaWebController::class, 'resultadoPdfPublic'])->name('encuestas.public.resultado.pdf');
Route::get('/encuestas/acceso/{codigo_acceso}/detalles/{resultado}', [EncuestaWebController::class, 'detallesPublic'])->name('encuestas.public.detalles');

// ===== RUTAS DE API INTERNA (usadas por fetch() en el flujo de encuestas) =====
// Sin 'auth' porque también las usa el flujo público por código de acceso; el
// propio controlador exige que el código de acceso coincida cuando no hay sesión.
Route::prefix('api')->group(function () {
    Route::prefix('encuestas')->name('api.encuestas.')->group(function () {
        // Obtener una encuesta específica con sus preguntas
        Route::get('/{encuesta}', [ApiEncuestaController::class, 'show'])->name('encuestas.show');
    });

    Route::prefix('respuestas')->group(function () {
        // Guardar una respuesta individual
        Route::post('/{resultado}', [ApiEncuestaController::class, 'guardarRespuesta'])->name('respuestas.store');

        // Finalizar encuesta y generar análisis
        Route::post('/{resultado}/finalizar', [ApiEncuestaController::class, 'finalizar'])->name('respuestas.finalizar');
    });
});

// ===== RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN) =====
Route::middleware(['auth'])->group(function () {

    // Ruta de resumen general
    Route::get('/', [ResumenController::class, 'index'])->name('resumen');

    // Ruta de logout (protegida)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // RUTAS DE PREGUNTAS
    Route::prefix('/preguntas')->name('preguntas.')->middleware('permission:preguntas')->group(function () {
        Route::get('/', [PreguntaController::class, 'index'])->name('index');
        Route::get('/crear', [PreguntaController::class, 'create'])->name('create');
        Route::post('/guardar', [PreguntaController::class, 'store'])->name('store');
        Route::get('/{pregunta}/editar', [PreguntaController::class, 'show'])->name('show');
        Route::put('/{pregunta}', [PreguntaController::class, 'update'])->name('update');
        Route::delete('/{pregunta}', [PreguntaController::class, 'destroy'])->name('destroy');
    });

    // RUTAS DE DE ENCUESTAS
    Route::prefix('/encuestas')->name('encuestas.')->middleware('permission:encuestas')->group(function () {
        Route::get('/', [EncuestaController::class, 'index'])->name('index');
        Route::get('/crear', [EncuestaController::class, 'create'])->name('create');
        Route::post('/guardar', [EncuestaController::class, 'store'])->name('store');
        Route::get('/{encuesta}/editar', [EncuestaController::class, 'show'])->name('show');
        Route::put('/{encuesta}', [EncuestaController::class, 'update'])->name('update');
        Route::delete('/{encuesta}', [EncuestaController::class, 'destroy'])->name('destroy');
    });

    // RUTAS DE USUARIOS
    Route::prefix('/usuarios')->name('usuarios.')->middleware('permission:usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/crear', [UserController::class, 'create'])->name('create');
        Route::post('/guardar', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/editar', [UserController::class, 'show'])->name('show');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // RUTAS DE SEMESTRES
    Route::prefix('/semestres')->name('semestres.')->middleware('permission:semestres')->group(function () {
        Route::get('/', [SemestreController::class, 'index'])->name('index');
        Route::post('/guardar', [SemestreController::class, 'store'])->name('store');
        Route::post('/{semestre}/activar', [SemestreController::class, 'activar'])->name('activar');
        Route::delete('/{semestre}', [SemestreController::class, 'destroy'])->name('destroy');
    });

    //RUTAS PARA ENCUESTAS - ESTUDIANTE
    Route::middleware('permission:encuestas')->group(function () {
        // Página de estadísticas de encuesta
        Route::get('/encuestas/{encuesta}/estadisticas', [EncuestaWebController::class, 'estadisticas'])->name('estadisticas-encuesta');

        // PDF de estadísticas de encuesta
        Route::get('/encuestas/{encuesta}/estadisticas/pdf', [EncuestaWebController::class, 'estadisticasPdf'])->name('estadisticas-encuesta.pdf');

        // Página de responder encuesta
        Route::get('/respuestas/{resultado}/responder', [EncuestaWebController::class, 'responder'])->name('responder-encuesta');

        // Página de resultado de encuesta en estudiante
        Route::get('/respuestas/{resultado}/resultado', [EncuestaWebController::class, 'resultado'])->name('resultado-encuesta');

        // PDF de resultado de encuesta en estudiante
        Route::get('/respuestas/{resultado}/resultado/pdf', [EncuestaWebController::class, 'resultadoPdf'])->name('resultado-encuesta.pdf');

        // Página de detalles de resultado de encuesta en estudiante
        Route::get('/respuestas/{resultado}/detalles', [EncuestaWebController::class, 'detalles'])->name('detalles-encuesta');

        // Eliminar el resultado de un estudiante (sin importar si finalizó la encuesta o no)
        Route::delete('/respuestas/{resultado}', [EncuestaWebController::class, 'eliminarResultado'])->name('resultados.destroy');
    });
});
