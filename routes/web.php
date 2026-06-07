<?php

use Illuminate\Support\Facades\Route;

// ===== RUTAS PÚBLICAS (SIN AUTENTICACIÓN) =====

// Páginas de autenticación
Route::get('/login', function () {
    return view('pages.auth.signin', ['title' => 'Iniciar Sesión']);
})->name('login');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Registrarse']);
})->name('signup');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');

// Página de error 404
Route::fallback(function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
});


// ===== RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN) =====
Route::middleware(['auth'])->group(function () {

    // Ruta de logout (protegida)
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    // RUTAS DE PREGUNTAS
    Route::prefix('/preguntas')->name('preguntas.')->group(function () {
        Route::get('/', [App\Http\Controllers\PreguntaController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\PreguntaController::class, 'create'])->name('create');
        Route::post('/guardar', [App\Http\Controllers\PreguntaController::class, 'store'])->name('store');
        Route::get('/{pregunta}/editar', [App\Http\Controllers\PreguntaController::class, 'show'])->name('show');
        Route::put('/{pregunta}', [App\Http\Controllers\PreguntaController::class, 'update'])->name('update');
        Route::delete('/{pregunta}', [App\Http\Controllers\PreguntaController::class, 'destroy'])->name('destroy');
    });

    // RUTAS DE DE ENCUESTAS
    Route::prefix('/encuestas')->name('encuestas.')->group(function () {
        Route::get('/', [App\Http\Controllers\EncuestaController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\EncuestaController::class, 'create'])->name('create');
        Route::post('/guardar', [App\Http\Controllers\EncuestaController::class, 'store'])->name('store');
        Route::get('/{encuesta}/editar', [App\Http\Controllers\EncuestaController::class, 'show'])->name('show');
        Route::put('/{encuesta}', [App\Http\Controllers\EncuestaController::class, 'update'])->name('update');
        Route::delete('/{encuesta}', [App\Http\Controllers\EncuestaController::class, 'destroy'])->name('destroy');
    });

    //RUTAS PARA ENCUESTAS - ESTUDIANTE

    // Página de estadísticas de encuesta
    Route::get('/encuestas/{encuesta}/estadisticas', [App\Http\Controllers\EncuestaWebController::class, 'estadisticas'])->name('estadisticas-encuesta');

    //Pagina de iniciar encuesta
    Route::get('/encuestas/{encuesta}/iniciar', [App\Http\Controllers\EncuestaWebController::class, 'iniciar'])->name('iniciar-encuesta');

    // Guardar datos iniciales y redirigir a responder encuesta
    Route::post('/encuestas/{encuesta}/guardar-datos', [App\Http\Controllers\EncuestaWebController::class, 'store'])->name('guardar-datos-encuesta');

    // Página de responder encuesta
    Route::get('/respuestas/{resultado}/responder', [App\Http\Controllers\EncuestaWebController::class, 'responder'])->name('responder-encuesta');

    // Página de resultado de encuesta en estudiante
    Route::get('/respuestas/{resultado}/resultado', [App\Http\Controllers\EncuestaWebController::class, 'resultado'])->name('resultado-encuesta');

    // Página de detalles de resultado de encuesta en estudiante
    Route::get('/respuestas/{resultado}/detalles', [App\Http\Controllers\EncuestaWebController::class, 'detalles'])->name('detalles-encuesta');

    // dashboard pages
    Route::get('/', function () {
        return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
    })->name('dashboard');

    // calender pages
    Route::get('/calendar', function () {
        return view('pages.calender', ['title' => 'Calendar']);
    })->name('calendar');

    // profile pages
    Route::get('/profile', function () {
        return view('pages.profile', ['title' => 'Profile']);
    })->name('profile');

    // form pages
    Route::get('/form-elements', function () {
        return view('pages.form.form-elements', ['title' => 'Form Elements']);
    })->name('form-elements');

    // tables pages
    Route::get('/basic-tables', function () {
        return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
    })->name('basic-tables');

    // pages
    Route::get('/blank', function () {
        return view('pages.blank', ['title' => 'Blank']);
    })->name('blank');

    // chart pages
    Route::get('/line-chart', function () {
        return view('pages.chart.line-chart', ['title' => 'Line Chart']);
    })->name('line-chart');

    Route::get('/bar-chart', function () {
        return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
    })->name('bar-chart');

    // ui elements pages
    Route::get('/alerts', function () {
        return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
    })->name('alerts');

    Route::get('/avatars', function () {
        return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
    })->name('avatars');

    Route::get('/badge', function () {
        return view('pages.ui-elements.badges', ['title' => 'Badges']);
    })->name('badges');

    Route::get('/buttons', function () {
        return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
    })->name('buttons');

    Route::get('/image', function () {
        return view('pages.ui-elements.images', ['title' => 'Images']);
    })->name('images');

    Route::get('/videos', function () {
        return view('pages.ui-elements.videos', ['title' => 'Videos']);
    })->name('videos');
});

// Fallback route for 404 errors
Route::fallback(function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
});
