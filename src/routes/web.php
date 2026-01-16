<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ProfileController;
use App\Models\Category; // <--- 1. NUEVO: Importamos el modelo
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookSearchController; // <--- IMPORTANTE: Esto arriba del todo

Route::get('/', function () {
    return view('welcome');
});

// 2. NUEVO: Ruta Dashboard modificada para enviar datos
Route::get('/dashboard', function () {
    // Obtenemos las categorías para mostrarlas en el panel
    $categories = Category::all(); 
    return view('dashboard', compact('categories'));
})->middleware(['auth', 'verified'])->name('dashboard');


// GRUPO DE RUTAS PROTEGIDAS (Solo usuarios logueados)
Route::middleware(['auth', 'verified'])->group(function () {

    // =================================================================
    //  RELACIÓN 1:1 - GESTIÓN DE PERFIL
    // =================================================================
    
    // Ver mi perfil
    Route::get('/mi-perfil', [ProfileController::class, 'index'])->name('profile.index');

    // Gestión (PD1) - Rutas de creación primero
    Route::get('/mi-perfil/crear', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/mi-perfil', [ProfileController::class, 'store'])->name('profile.store');

    // Rutas con parámetros
    Route::get('/mi-perfil/{profile}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mi-perfil/{profile}/editar', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mi-perfil/{profile}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/mi-perfil/{profile}', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // =================================================================
    //  ENTIDAD 1: CATEGORÍAS (CategoryController)
    // =================================================================
    
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');

    // IMPORTANTE: La ruta 'crear' DEBE ir antes que la ruta dinámica '{category}'
    Route::get('/categorias/crear', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categories.store');

    // Rutas que requieren un ID (Model Binding: {category} coincide con $category en el controller)
    Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categorias/{category}/editar', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // =================================================================
    //  ENTIDAD 2: LIBROS (BookController)
    // =================================================================
    
    Route::get('/libros', [BookController::class, 'index'])->name('books.index');

    // 'crear' antes que '{book}'
    Route::get('/libros/crear', [BookController::class, 'create'])->name('books.create');
    Route::post('/libros', [BookController::class, 'store'])->name('books.store');

    // Rutas dinámicas (Usamos {book})
    Route::get('/libros/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/libros/{book}/editar', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/libros/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/libros/{book}', [BookController::class, 'destroy'])->name('books.destroy');


    // =================================================================
    //  ENTIDAD 3: AUTORES (AuthorController)
    // =================================================================
    
    Route::get('/autores', [AuthorController::class, 'index'])->name('authors.index');

    // 'crear' antes que '{author}'
    Route::get('/autores/crear', [AuthorController::class, 'create'])->name('authors.create');
    Route::post('/autores', [AuthorController::class, 'store'])->name('authors.store');

    // Rutas dinámicas (Usamos {author})
    Route::get('/autores/{author}', [AuthorController::class, 'show'])->name('authors.show');
    Route::get('/autores/{author}/editar', [AuthorController::class, 'edit'])->name('authors.edit');
    Route::put('/autores/{author}', [AuthorController::class, 'update'])->name('authors.update');
    Route::delete('/autores/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');

Route::get('/scan', [BookSearchController::class, 'index'])->name('books.search');
Route::post('/scan', [BookSearchController::class, 'search'])->name('books.process');
// Ruta para el filtro por categorías
Route::get('/browse', [BookSearchController::class, 'browseByCategory'])->name('books.browse');

});

require __DIR__.'/auth.php';