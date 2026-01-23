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
     */
    public function scan(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json([]);
        }

        $url = "https://openlibrary.org/search.json?q=" . urlencode($query) . "&limit=12";

        try {
            $response = Http::get($url);
            
            if ($response->failed()) {
                return response()->json(['error' => 'No se pudo conectar con OpenLibrary'], 500);
            }

            $data = $response->json();
            $books = [];

            if (isset($data['docs'])) {
                foreach ($data['docs'] as $item) {
                    $books[] = [
                        'title' => $item['title'] ?? 'Sin título',
                        'author' => isset($item['author_name']) ? implode(', ', $item['author_name']) : 'Desconocido',
                        'year' => $item['first_publish_year'] ?? null,
                        'isbn' => isset($item['isbn']) ? $item['isbn'][0] : null,
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
     * API: Listar libros (CONFIGURADO A 500)
     */
    public function index(Request $request)
    {
        $query = Book::where('user_id', Auth::id())->with('category');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // ¡AQUÍ ESTÁ EL CAMBIO!
        // Cargamos 500 libros por página.
        $books = $query->latest()->paginate(10000); 

        return response()->json($books);
    }

    /**
     * API: Guardar libro
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string',
            'isbn' => 'nullable|string',
            'year' => 'nullable|integer',
            'cover_url' => 'nullable|string',
            'suggested_category' => 'nullable|string'
        ]);

        $categoryId = null;
        if (!empty($request->suggested_category)) {
            $category = Category::where('name', 'LIKE', '%' . $request->suggested_category . '%')->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }
        if ($request->has('category_id')) {
            $categoryId = $request->category_id;
        }

        $book = Book::create([
            'user_id' => Auth::id(),
            'category_id' => $categoryId,
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'year' => $request->year,
            'cover_url' => $request->cover_url,
            'status' => 'pending'
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
     * API: Ver un solo libro
     */
    public function show($id)
    {
        $book = Book::where('user_id', Auth::id())->with('category')->find($id);
        
        if (!$book) return response()->json(['error' => 'No encontrado'], 404);
        
        return response()->json($book);
    }
}