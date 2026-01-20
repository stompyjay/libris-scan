<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookSearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Todo protegido por autenticación (auth:sanctum es el estándar para API en Laravel)
Route::middleware(['auth:sanctum'])->group(function () {

    // 1. USUARIO ACTUAL
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 2. DASHBOARD (Datos resumen)
    Route::get('/dashboard-stats', function () {
        return response()->json([
            'categories' => \App\Models\Category::all(),
            'total_books' => \App\Models\Book::count(),
            // Agrega aquí lo que necesites pintar en el dashboard
        ]);
    });

    // 3. RECURSOS COMPLETOS (CRUD)
    // apiResource crea automáticamente rutas para: index, store, show, update, destroy
    Route::apiResource('categorias', CategoryController::class);
    Route::apiResource('libros', BookController::class);
    Route::apiResource('autores', AuthorController::class);
    
    // Perfil (Como es relación 1:1, lo hacemos manual)
    Route::get('/mi-perfil', [ProfileController::class, 'index']);
    Route::post('/mi-perfil', [ProfileController::class, 'store']);
    Route::put('/mi-perfil/{profile}', [ProfileController::class, 'update']);

    // 4. BÚSQUEDA Y ESCÁNER
    Route::get('/scan', [BookSearchController::class, 'index']);   // Para buscar (GET)
    Route::post('/scan', [BookSearchController::class, 'search']); // Para guardar (POST) - Nota: mantuve el nombre 'search' porque así lo tenías en routes, pero actúa como 'store'
    Route::get('/browse', [BookSearchController::class, 'browseByCategory']); // Para explorar

});