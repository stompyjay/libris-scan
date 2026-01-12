<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ReviewController;

Route::middleware(['auth', 'verified'])->group(function () {

    // --- ENTIDAD 1: CATEGORÍAS (Relación 1:N) ---
    // Listado (PD1)
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
    // Ver Detalle de Entidad 1 (Muestra todos sus Libros - Requisito 2)
    Route::get('/categorias/{id}', [CategoryController::class, 'show'])->name('categories.show');
    // Gestión (PD1)
    Route::get('/categorias/crear', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categorias/{id}/editar', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categorias/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categorias/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // --- ENTIDAD 2: LIBROS (Entidad Central) ---
    // Listado con Filtro por Entidad 1 (Requisito 2 - Búsqueda)
    Route::get('/libros', [BookController::class, 'index'])->name('books.index');
    // Detalle de Entidad 2 (Muestra su Categoría y sus Autores - Requisito 2 y 3)
    Route::get('/libros/{id}', [BookController::class, 'show'])->name('books.show');
    // Gestión (PD1)
    Route::get('/libros/crear', [BookController::class, 'create'])->name('books.create');
    Route::post('/libros', [BookController::class, 'store'])->name('books.store');
    Route::get('/libros/{id}/editar', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/libros/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/libros/{id}', [BookController::class, 'destroy'])->name('books.destroy');


    // --- ENTIDAD 3: AUTORES (Relación N:M) ---
    // Listado de Autores (Requisito 3 - Navegación)
    Route::get('/autores', [AuthorController::class, 'index'])->name('authors.index');
    // Ver Detalle de Autor (Muestra todos sus libros asociados - Requisito 3)
    Route::get('/autores/{id}', [AuthorController::class, 'show'])->name('authors.show');
    // Gestión (PD1)
    Route::get('/autores/crear', [AuthorController::class, 'create'])->name('authors.create');
    Route::post('/autores', [AuthorController::class, 'store'])->name('authors.store');
    Route::get('/autores/{id}/editar', [AuthorController::class, 'edit'])->name('authors.edit');
    Route::put('/autores/{id}', [AuthorController::class, 'update'])->name('authors.update');
    Route::delete('/autores/{id}', [AuthorController::class, 'destroy'])->name('authors.destroy');

});

require __DIR__.'/auth.php';
