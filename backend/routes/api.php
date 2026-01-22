<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Importamos TODOS los controladores
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookSearchController;
use App\Http\Controllers\ReviewController; // <--- Faltaba este

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (Abiertas a todo el mundo)
|--------------------------------------------------------------------------
| Aquí van las cosas que se ven en la Landing Page (index.html)
*/
Route::get('/reviews', [ReviewController::class, 'index']);


/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Solo usuarios logueados)
|--------------------------------------------------------------------------
| Usamos 'web' + 'auth' para compartir la sesión del login.
*/
Route::middleware(['web', 'auth'])->group(function () {

    // --- USUARIO Y DASHBOARD ---
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard-stats', function () {
        return response()->json([
            'categories_count' => \App\Models\Category::count(),
            'books_count'      => \App\Models\Book::where('user_id', auth()->id())->count(),
            'authors_count'    => \App\Models\Author::count(),
            'reading_now'      => \App\Models\Book::where('user_id', auth()->id())
                                                ->where('status', 'reading')->count()
        ]);
    });

    // --- RECURSOS COMPLETOS (CRUD AUTOMÁTICO) ---
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('books',      BookController::class);
    Route::apiResource('authors',    AuthorController::class);
    
    // --- PERFIL DE USUARIO ---
    Route::get('/profile', [ProfileController::class, 'index']); 
    Route::put('/profile', [ProfileController::class, 'update']); 

    // --- BÚSQUEDA Y ESCÁNER ---
    Route::get('/scan',   [BookSearchController::class, 'index']); // Buscar (GET)
    
    // OJO AQUÍ: He puesto 'search' porque así se llamaba en tu controlador anterior.
    // Si en el controlador lo cambiaste a 'store', cambia aquí 'search' por 'store'.
    Route::post('/scan',  [BookSearchController::class, 'search']); 
    
    Route::get('/browse', [BookSearchController::class, 'browseByCategory']);

    Route::post('/purchase', [PurchaseController::class, 'store']);
});