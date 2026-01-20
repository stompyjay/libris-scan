<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * LISTAR AUTORES
     * Importante: Esto lo usará tu formulario de "Crear Libro" 
     * para llenar el select múltiple.
     */
    public function index()
    {
        // Devolvemos todos los autores ordenados alfabéticamente
        $authors = Author::orderBy('name', 'asc')->get();
        return response()->json($authors);
    }

    /**
     * CREAR UN NUEVO AUTOR
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
            'bio'  => 'nullable|string',
        ]);

        $author = Author::create($validated);

        return response()->json([
            'message' => 'Autor creado con éxito',
            'author' => $author
        ], 201);
    }

    /**
     * VER UN AUTOR (Y SUS LIBROS)
     * Aquí demostramos la relación N:M inversa.
     */
    public function show(Author $author)
    {
        // Cargamos la relación 'books' para ver qué libros ha escrito este señor
        $author->load('books'); 
        
        return response()->json($author);
    }

    /**
     * ACTUALIZAR AUTOR
     */
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio'  => 'nullable|string',
        ]);

        $author->update($validated);

        return response()->json([
            'message' => 'Autor actualizado',
            'author' => $author
        ]);
    }

    /**
     * ELIMINAR AUTOR
     */
    public function destroy(Author $author)
    {
        // Al borrar el autor, se borra automáticamente de la tabla pivote 'author_book'
        // pero los libros NO se borran (solo se quedan sin este autor).
        $author->delete();

        return response()->json(null, 204);
    }
}