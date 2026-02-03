<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ProfileController;

// 1. --- LANDING PAGE ---
// Comentado para que Nginx cargue el index.html del Frontend. ¡PERFECTO!
/* Route::get('/', function () {
    return redirect('/login');
}); */

// 2. --- LOGOUT MANUAL ---
// Al invocar esta ruta, limpiamos todo y mandamos a la Landing
Route::get('/logout-manual', function () {
    Auth::logout();
    Session::flush();
    return redirect('/'); // Nginx detectará '/' y mostrará index.html
})->name('logout.manual');

// 3. --- PERFIL (Rutas protegidas) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Si usas borrar cuenta, descomenta esta:
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 4. --- RUTAS DE AUTH (Login, Register, Password Reset...) ---
require __DIR__.'/auth.php';