<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;

// ... (El resto de tus rutas públicas y logout siguen igual) ...

// 3. --- RUTAS PROTEGIDAS (Solo usuarios logueados) ---
Route::middleware('auth')->group(function () {
    
    // Rutas existentes...
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/purchase', [BookController::class, 'purchase'])->name('books.purchase');
    Route::get('/my-books', [BookController::class, 'myBooks']);

    // --- NUEVA RUTA PARA PD3 (AGREGA ESTO) ---
    // Esta ruta devuelve el objeto usuario (id, name, email, admin...) en JSON
    Route::get('/user-info', function () {
        return response()->json(auth()->user());
    });

    // Opcional: Si en el futuro quieres una ruta que SOLO el admin pueda ver:
    // Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->get('/admin-test', function() {
    //     return "Hola Admin, esta zona es secreta.";
    // });
});

require __DIR__.'/auth.php';