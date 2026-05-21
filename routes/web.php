<?php

use Illuminate\Support\Facades\Route;

Route::get('/preguntas', [App\Http\Controllers\PreguntaController::class, 'index'])->name('preguntas');
Route::get('/preguntas/crear', [App\Http\Controllers\PreguntaController::class, 'create'])->name('preguntas-create');
Route::post('/preguntas/guardar', [App\Http\Controllers\PreguntaController::class, 'store'])->name('preguntas.store');
Route::get('/preguntas/{pregunta}/editar', [App\Http\Controllers\PreguntaController::class, 'edit'])->name('preguntas.edit');
Route::put('/preguntas/{pregunta}', [App\Http\Controllers\PreguntaController::class, 'update'])->name('preguntas.update');
Route::delete('/preguntas/{pregunta}', [App\Http\Controllers\PreguntaController::class, 'destroy'])->name('preguntas.destroy');

// RUTAS DE ADMINISTRACIÓN DE ENCUESTAS
Route::prefix('admin/encuestas')->name('encuestas.')->group(function () {
    Route::get('/', [App\Http\Controllers\EncuestaController::class, 'index'])->name('index');
    Route::get('/crear', [App\Http\Controllers\EncuestaController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\EncuestaController::class, 'store'])->name('store');
    Route::get('/{encuesta}/editar', [App\Http\Controllers\EncuestaController::class, 'edit'])->name('edit');
    Route::put('/{encuesta}', [App\Http\Controllers\EncuestaController::class, 'update'])->name('update');
    Route::delete('/{encuesta}', [App\Http\Controllers\EncuestaController::class, 'destroy'])->name('destroy');
    Route::get('/{encuesta}/asignar-preguntas', [App\Http\Controllers\EncuestaController::class, 'asignarPreguntasForm'])->name('asignar-preguntas');
    Route::post('/{encuesta}/asignar-preguntas', [App\Http\Controllers\EncuestaController::class, 'asignarPreguntas'])->name('asignar-preguntas-store');
});

//RUTAS PARA ENCUESTAS - ESTUDIANTE
//Lista de encuestas
Route::get('/encuestas', [App\Http\Controllers\EncuestaWebController::class, 'index'])->name('encuestas');

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






// authentication routes
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');


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

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

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

// Fallback route for 404 errors
Route::fallback(function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
});
