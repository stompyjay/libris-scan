<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login'); // O redirige a 'shop.index' si prefieres
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Necesitas Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 1. EL TABLERO (books.blade.php)
    // En tu HTML usas route('books.index') y url('/dashboard'), unificamos aquí:
    Route::get('/books', [BookController::class, 'index'])->name('books.index');

    // Drag & Drop: Actualizar estado del libro
    Route::patch('/books/{id}/status', [BookController::class, 'updateStatus'])->name('books.updateStatus');


    // 2. LA TIENDA (shop.blade.php)
    Route::get('/shop', [BookController::class, 'shop'])->name('shop.index');


    // 3. EL PAGO (checkout.blade.php)
    // Necesario porque tu JS hace: window.location.href = "{{ route('checkout.show') }}"
    Route::get('/checkout', [BookController::class, 'checkout'])->name('checkout.show');
    
    // Procesar la compra (POST)
    Route::post('/purchase', [BookController::class, 'purchase'])->name('books.purchase');


    // 4. PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 5. UTILIDADES
    Route::get('/user-info', function () {
        return response()->json(auth()->user());
    });
});