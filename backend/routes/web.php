<?php

use Illuminate\Support\Facades\Route;

// 1. La raíz redirige al login (o a tu index.html si prefieres)
Route::get('/', function () {
    return redirect('/login');
});

// 2. Ruta para proteger el cierre de sesión y redirección post-login
Route::middleware('auth')->group(function () {
    // Aquí podrías dejar rutas muy específicas que necesiten sesión web
    // pero idealmente, casi todo se va.
});

Route::get('/redirigir-dashboard', function () {
    return redirect('/dashboard.html'); 
})->middleware(['auth'])->name('dashboard');

// 3. ESTO ES LO IMPORTANTE: Las rutas de autenticación (Login, Register)
require __DIR__.'/auth.php';