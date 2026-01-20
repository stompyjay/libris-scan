<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * API: Obtener lista de libros del usuario (JSON)
     */
    public function index()
    {
        // Recuperamos los libros paginados (igual que antes)
        $books = Book::where('user_id', Auth::id())
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        // Devolvemos JSON directo
        return response()->json($books);
    }

    /**
     * NOTA: Se han eliminado create() y edit()
     * Las APIs no sirven HTML (formularios), eso lo construye tu JavaScript en el frontend.
     */

    /**
     * API: Guardar un libro nuevo
     */
    public function store(Request $request)
    {
        // 1. Validación (La mantenemos igual)
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,reading,completed,borrowed',
        ]);

        // 2. Creación vinculada al usuario
        $book = $request->user()->books()->create($validated);

        // 3. Respuesta JSON (Código 201 = Created)
        return response()->json([
            'message' => 'Libro creado correctamente',
            'book'    => $book
        ], 201);
    }

    /**
     * API: Ver un solo libro
     */
    public function show(Book $book)
    {
        // Seguridad: Verificar dueño
        if ($book->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($book);
    }

    /**
     * API: Actualizar libro
     */
    public function update(Request $request, Book $book)
    {
        // Seguridad
        if ($book->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Validación
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'required|in:pending,reading,completed,borrowed',
            'description' => 'nullable|string',
        ]);

        // Actualizar
        $book->update($validated);

        // Devolvemos el libro actualizado
        return response()->json([
            'message' => 'Libro actualizado',
            'book'    => $book
        ]);
    }

    /**
     * API: Eliminar libro
     */
    public function destroy(Book $book)
    {
        // Seguridad
        if ($book->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $book->delete();

        // Código 204 = No Content (Operación exitosa, sin contenido que devolver)
        return response()->json(null, 204);
    }
}