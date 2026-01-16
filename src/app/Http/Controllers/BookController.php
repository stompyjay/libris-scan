<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category; // Necesario para pasar categorías a los formularios
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Muestra la lista de TUS libros.
     */
    public function index()
    {
        // Obtenemos solo los libros del usuario conectado, paginados de 10 en 10
        $books = Book::where('user_id', Auth::id())
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('books.index', compact('books'));
    }

    /**
     * Muestra el formulario para crear un libro manualmente (si no usas el escáner).
     */
    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    /**
     * Guarda un libro nuevo en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validamos que los datos vengan bien
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,reading,completed,borrowed',
        ]);

        // 2. Creamos el libro vinculado al usuario actual
        // Al usar $request->user()->books()->create(...), Laravel rellena el user_id solo
        $request->user()->books()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Libro añadido correctamente.');
    }

    /**
     * Muestra los detalles de un libro específico.
     */
    public function show(Book $book)
    {
        // Seguridad: Verificar que el libro es del usuario
        if ($book->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este libro.');
        }

        return view('books.show', compact('book'));
    }

    /**
     * Muestra el formulario para editar un libro.
     */
    public function edit(Book $book)
    {
        // Seguridad
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Actualiza los datos del libro en la base de datos.
     */
    public function update(Request $request, Book $book)
    {
        // Seguridad
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        // Validación
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:pending,reading,completed,borrowed',
            'description' => 'nullable|string',
        ]);

        // Actualizar
        $book->update($validated);

        return redirect()->route('dashboard')->with('success', 'Libro actualizado correctamente.');
    }

    /**
     * Elimina el libro de la base de datos.
     */
    public function destroy(Book $book)
    {
        // Seguridad: Solo el dueño puede borrarlo
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        $book->delete();

        return redirect()->route('dashboard')->with('success', 'Libro eliminado.');
    }
}