<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * API: BÚSQUEDA INTELIGENTE (Escáner)
     * Busca en OpenLibrary por Título, Autor o ISBN automáticamente.
     */
    public function scan(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json([]);
        }

        // Conectamos con OpenLibrary
        // "q=" busca en todos los campos a la vez (magia)
        $url = "https://openlibrary.org/search.json?q=" . urlencode($query) . "&limit=12";

        try {
            // Usamos el cliente HTTP de Laravel para más seguridad
            $response = Http::get($url);
            
            if ($response->failed()) {
                return response()->json(['error' => 'No se pudo conectar con OpenLibrary'], 500);
            }

            $data = $response->json();
            $books = [];

            if (isset($data['docs'])) {
                foreach ($data['docs'] as $item) {
                    // Procesamos los datos para que el frontend los entienda
                    $books[] = [
                        'title' => $item['title'] ?? 'Sin título',
                        'author' => isset($item['author_name']) ? implode(', ', $item['author_name']) : 'Desconocido',
                        'year' => $item['first_publish_year'] ?? null,
                        'isbn' => isset($item['isbn']) ? $item['isbn'][0] : null, // Cogemos el primer ISBN
                        'cover_id' => $item['cover_i'] ?? null,
                        'suggested_category' => isset($item['subject']) ? $item['subject'][0] : null
                    ];
                }
            }

            return response()->json($books);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error del servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Listar libros (Con filtro de Categoría)
     */
    public function index(Request $request)
    {
        // 1. Consulta base: solo libros del usuario
        $query = Book::where('user_id', Auth::id())->with('category');

        // 2. Filtro: Si recibimos category_id, filtramos
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // 3. Ordenar por más recientes
        $books = $query->latest()->get();

        return response()->json($books);
    }

    /**
     * API: Guardar libro (Desde el Escáner)
     */
    public function store(Request $request)
    {
        // Validación simple compatible con el escáner
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string', // Guardamos autor como texto
            'isbn' => 'nullable|string',
            'year' => 'nullable|integer',
            'cover_url' => 'nullable|string',
            'suggested_category' => 'nullable|string' // Lo que sugiere la API
        ]);

        // AUTO-CATEGORIZACIÓN
        // Si la API sugiere "Fiction", buscamos si existe esa categoría en tu BD
        $categoryId = null;
        if (!empty($request->suggested_category)) {
            // Buscamos una categoría parecida
            $category = Category::where('name', 'LIKE', '%' . $request->suggested_category . '%')->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }
        // Si el usuario eligió una manualmente en un select (opcional)
        if ($request->has('category_id')) {
            $categoryId = $request->category_id;
        }

        // Crear el libro
        $book = Book::create([
            'user_id' => Auth::id(),
            'category_id' => $categoryId,
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'year' => $request->year,
            'cover_url' => $request->cover_url,
            'status' => 'pending' // Estado por defecto
        ]);

        return response()->json([
            'message' => 'Libro guardado exitosamente',
            'book' => $book
        ], 201);
    }

    /**
     * API: Eliminar libro
     */
    public function destroy($id)
    {
        $book = Book::where('user_id', Auth::id())->where('id', $id)->first();

        if ($book) {
            $book->delete();
            return response()->json(['message' => 'Libro eliminado']);
        }

        return response()->json(['error' => 'No encontrado'], 404);
    }
    
    /**
     * API: Ver un solo libro (Opcional, para detalles)
     */
    public function show($id)
    {
        $book = Book::where('user_id', Auth::id())->with('category')->find($id);
        
        if (!$book) return response()->json(['error' => 'No encontrado'], 404);
        
        return response()->json($book);
    }
}