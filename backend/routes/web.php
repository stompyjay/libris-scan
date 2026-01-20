<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// 1. --- BORRA O COMENTA ESTO ---
// Al quitar esto, cuando vayas a '/', Nginx será libre de mostrar tu index.html
/* Route::get('/', function () {
    return redirect('/login');
});
*/

// 2. --- RUTA DE SALIDA (Déjala tal cual) ---
Route::get('/logout-manual', function () {
    Auth::logout();
    Session::flush();
    return redirect('/'); // Ahora sí, esto te dejará en la Landing Page
})->name('logout.manual');

// 3. --- EL PUENTE DEL DASHBOARD ---
Route::get('/redirigir-dashboard', function () {
    return redirect('/dashboard.html'); 
})->middleware(['auth'])->name('dashboard');

// 4. --- RUTAS DE AUTH ---
require __DIR__.'/auth.php';