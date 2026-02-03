<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    /**
     * API: BÚSQUEDA INTELIGENTE (OpenLibrary)
     */
    public function scan(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json([]);
        }

        // Buscamos en OpenLibrary
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
                        'cover_id' => $item['cover_i'] ?? null, // ID para construir la URL luego
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
     * API: LISTAR MIS LIBROS
     */
    public function myBooks(Request $request)
    {
        $user = $request->user();

        // --- SALVAVIDAS (AÑADE ESTO) ---
        // Si no detecta sesión real, cogemos el primer usuario de la base de datos
        // Así nunca te dará error 401 mientras programas el diseño
        if (!$user) {
            $user = \App\Models\User::first();
        }
        // -------------------------------

        if (!$user) {
            return response()->json([]); // Si la BD está vacía
        }

        try {
            $books = $user->books()
                          ->with('category')
                          ->withPivot('status')
                          ->orderBy('book_user.created_at', 'desc')
                          ->get();

            $books->transform(function ($book) {
                $book->status = $book->pivot->status; 
                return $book;
            });

            return response()->json($books);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: PROCESAR COMPRA (Carrito / Varios libros)
     * ESTA ES LA QUE TE FALTABA
     */
    public function purchase(Request $request)
{
    $user = $request->user();
    
    // Validamos
    $request->validate([
        'books' => 'required|array',
    ]);

    $purchasedBooks = [];

    foreach ($request->books as $item) {
        
        // ---------------------------------------------------------
        // 1. LÓGICA DE CATEGORÍAS AUTOMÁTICAS
        // ---------------------------------------------------------
        
        // Intentamos coger la categoría que viene de la API (suggested_category)
        // Si viene vacía, le ponemos 'General'
        $nombreCategoria = $item['suggested_category'] ?? 'General';
        
        // Limpiamos el texto (Ej: "Fiction" -> "Fiction")
        // Opcional: podrías traducir aquí si quisieras, pero dejémoslo simple
        $nombreCategoria = ucfirst(trim($nombreCategoria));

        // ¡AQUÍ ESTÁ LA CLAVE!
        // Busca una categoría con ese nombre. Si no existe, la crea en la BD.
        $category = \App\Models\Category::firstOrCreate(
            ['name' => $nombreCategoria], 
            ['description' => 'Categoría importada automáticamente']
        );
        
        $categoryId = $category->id; // Ya tenemos el ID (sea nuevo o viejo)

        // ---------------------------------------------------------
        // 2. LÓGICA DE PORTADA
        // ---------------------------------------------------------
        $finalCoverUrl = $item['cover'] ?? null;
        if (!empty($item['cover_id'])) {
            $finalCoverUrl = "https://covers.openlibrary.org/b/id/" . $item['cover_id'] . "-L.jpg";
        }

        // ---------------------------------------------------------
        // 3. GUARDAR EL LIBRO
        // ---------------------------------------------------------
        $book = \App\Models\Book::firstOrCreate(
            [
                'title'   => $item['title'], 
                'user_id' => $user->id // Asumiendo que quieres guardar el dueño aquí también
            ],
            [
                'author'      => $item['author'] ?? 'Desconocido',
                'isbn'        => $item['isbn'] ?? null,
                'cover'       => $finalCoverUrl,
                'price'       => $item['price'] ?? 10.00,
                'category_id' => $categoryId, // <--- Aquí asignamos el ID que acabamos de conseguir
                'description' => 'Importado el ' . now()->format('d/m/Y'),
                'status'      => 'pending'
            ]
        );
        
        // Si usas tabla pivote book_user también:
        if (!$user->books()->where('book_id', $book->id)->exists()) {
             $user->books()->attach($book->id, ['status' => 'pending']);
        }

        $purchasedBooks[] = $book;
    }

    return response()->json([
        'message' => 'Libros guardados y categorías actualizadas',
        'books'   => $purchasedBooks
    ], 201);
}

    /**
     * API: GUARDAR UN SOLO LIBRO (Desde búsqueda manual)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'cover_id' => 'required',
        ]);

        // 1. Gestionar Categoría
        $categoryId = null;

        if ($request->has('category_id') && $request->category_id) {
            $categoryId = $request->category_id;
        } 
        elseif ($request->filled('suggested_category')) {
            $category = Category::firstOrCreate(
                ['name' => ucfirst($request->suggested_category)]
            );
            $categoryId = $category->id;
        }

        // 2. Crear el libro CON PRECIO
        $book = Book::firstOrCreate(
            ['cover_id' => $request->cover_id], 
            [
                'title' => $request->title,
                'author' => $request->author ?? 'Desconocido',
                'isbn' => $request->isbn,
                'category_id' => $categoryId,
                'price' => 12.50, // <--- PRECIO AÑADIDO (Ejemplo diferente para individuales)
            ]
        );

        // 3. Vincular al usuario
        $user = $request->user();

        if (!$user->books()->where('book_id', $book->id)->exists()) {
            $user->books()->attach($book->id, ['status' => 'pending']);
            $message = 'Libro añadido a tu biblioteca';
        } else {
            return response()->json(['message' => 'Ya tienes este libro'], 409);
        }

        return response()->json(['message' => $message, 'book' => $book], 201);
    }

    /**
     * API: ELIMINAR LIBRO DE MI BIBLIOTECA
     */
    public function destroy(Request $request, $id)
    {
        $detached = $request->user()->books()->detach($id);

        if ($detached) {
            return response()->json(['message' => 'Libro eliminado']);
        }

        return response()->json(['error' => 'No encontrado'], 404);
    }
    
    /**
     * API: VER DETALLE DE UN LIBRO
     */
    public function show(Request $request, $id)
    {
        $book = $request->user()->books()
                        ->with('category')
                        ->withPivot('status')
                        ->find($id);
        
        if (!$book) return response()->json(['error' => 'No encontrado'], 404);
        
        $book->status = $book->pivot->status;

        return response()->json($book);
    }

    /**
     * API: ACTUALIZAR ESTADO (Leyendo, Completado...)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reading,completed,dropped'
        ]);

        $request->user()->books()->updateExistingPivot($id, [
            'status' => $request->status
        ]);

        return response()->json(['message' => 'Estado actualizado']);
    }
}