<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookSearchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (Accesibles sin login)
|--------------------------------------------------------------------------
*/

// --- ¡LA SOLUCIÓN! ---
// La hemos movido aquí arriba. Ahora Laravel dejará pasar la petición, 
// y tu controlador usará el User::first() para mostrar los datos.
Route::get('/my-books', [BookController::class, 'myBooks']);

// Otras rutas públicas
Route::get('/reviews', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store']);
Route::post('/purchase', [BookController::class, 'purchase']);


/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Requieren Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard-stats', function () {
        return response()->json([
            'categories_count' => \App\Models\Category::count(),
            'books_count'      => auth()->user()->books()->count(), 
            'authors_count'    => \App\Models\Author::count(),
            'reading_now'      => auth()->user()->books()->wherePivot('status', 'reading')->count()
        ]);
    });

    // Route::get('/my-books', ...); <--- ELIMINADA DE AQUÍ
    
    Route::patch('/books/{id}/status', [BookController::class, 'updateStatus']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('books',      BookController::class);
    Route::apiResource('authors',    AuthorController::class);

    Route::get('/scan',   [BookSearchController::class, 'index']);
    Route::post('/scan',  [BookSearchController::class, 'search']); 
    Route::get('/browse', [BookSearchController::class, 'browseByCategory']);

});