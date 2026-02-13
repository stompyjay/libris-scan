<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // ==========================================
    // VISTAS (PÁGINAS QUE SE VEN EN EL NAVEGADOR)
    // ==========================================

    /**
     * Muestra el Tablero de Lectura (Dashboard)
     * Ruta: /dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Si no hay usuario, redirigir al login (protección extra)
        if (!$user) {
            return redirect()->route('login');
        }

        // Recuperar los libros del usuario con sus relaciones
        $books = $user->books()
                      ->with(['category', 'authors'])
                      ->withPivot('status')
                      ->orderBy('book_user.created_at', 'desc')
                      ->get();

        // Ajustar el formato si es necesario para la vista
        $books->transform(function ($book) {
            $book->status = $book->pivot->status;
            return $book;
        });

        // Retorna la VISTA 'books.blade.php' con los datos
        return view('books', compact('books'));
    }

    /**
     * Muestra la Tienda de Libros
     * Ruta: /shop
     */
    public function shop()
    {
        // Aquí podrías mostrar todos los libros del sistema o los más populares
        // Por ahora enviamos todos para que la tienda no esté vacía
        $books = Book::with('authors')->inRandomOrder()->limit(20)->get();
        
        return view('shop', compact('books'));
    }

    /**
     * Muestra la Página de Pago
     * Ruta: /checkout
     */
    public function checkout()
    {
        return view('checkout');
    }

    // ==========================================
    // API / AJAX (ACCIONES DE JAVASCRIPT)
    // ==========================================

    // --- UPDATE STATUS (DRAG & DROP) ---
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        
        $user = $request->user();
        
        // Actualizar el estado en la tabla pivote (book_user)
        $user->books()->updateExistingPivot($id, ['status' => $request->status]);
        
        return response()->json(['message' => 'Estado actualizado']);
    }

    // --- SCAN (BUSCADOR OPENLIBRARY) ---
    public function scan(Request $request)
    {
        $query = $request->query('query');
        if (!$query) return response()->json([]);

        $url = "https://openlibrary.org/search.json?q=" . urlencode($query) . "&limit=12";

        try {
            $response = Http::get($url);
            if ($response->failed()) return response()->json(['error' => 'No se pudo conectar'], 500);

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
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // --- PURCHASE (PROCESAR COMPRA) ---
    public function purchase(Request $request)
    {
        // Nota: Si envías los datos por AJAX (fetch/axios), devuelve JSON.
        // Si usas un formulario HTML estándar, usa redirect().
        
        $request->validate(['books' => 'required|array']);
        $user = $request->user();

        $purchasedBooks = [];

        foreach ($request->books as $item) {
            // Lógica de creación/búsqueda de libro (Mantenida de tu código)
            $titulo = !empty($item['title']) ? $item['title'] : 'Libro sin título';
            $authorName = !empty($item['author']) ? $item['author'] : 'Desconocido';
            $catName = $item['suggested_category'] ?? 'General';
            
            $category = Category::firstOrCreate(['name' => ucfirst($catName)]);
            $authorModel = Author::firstOrCreate(['name' => $authorName]);

            $book = Book::updateOrCreate(
                ['title' => $titulo],
                [
                    'author'      => $authorName, 
                    'isbn'        => $item['isbn'] ?? null,
                    'cover'       => $item['cover'] ?? 'https://via.placeholder.com/150',
                    'price'       => $item['price'] ?? 10.00,
                    'category_id' => $category->id,
                    'description' => 'Comprado en la tienda',
                ]
            );

            $book->authors()->syncWithoutDetaching([$authorModel->id]);
            
            // Añadir al usuario con estado 'pending'
            $user->books()->syncWithoutDetaching([$book->id => ['status' => 'pending']]);

            $purchasedBooks[] = $book;
        }

        // Si la petición espera JSON (AJAX)
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Compra realizada', 'books' => $purchasedBooks], 200);
        }

        // Si es un formulario normal, redirige al Dashboard
        return redirect()->route('books.index')->with('success', 'Compra realizada con éxito');
    }

    // --- STORE (GUARDAR MANUAL - OPCIONAL SI USAS EL MODAL) ---
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $user = $request->user();

        $categoryName = $request->suggested_category ?? 'General';
        $category = Category::firstOrCreate(['name' => ucfirst($categoryName)]);

        $authorName = $request->author ?? 'Desconocido';
        $authorModel = Author::firstOrCreate(['name' => $authorName]);

        $book = Book::firstOrCreate(
            ['title' => $request->title], 
            [
                'author' => $authorName,
                'isbn' => $request->isbn,
                'cover' => $request->cover ?? 'https://via.placeholder.com/150',
                'category_id' => $category->id,
                'price' => 12.50,
                'description' => 'Añadido manualmente'
            ]
        );

        $book->authors()->syncWithoutDetaching([$authorModel->id]);

        if (!$user->books()->where('book_id', $book->id)->exists()) {
            $user->books()->attach($book->id, ['status' => 'pending']);
            $message = 'Libro añadido';
        } else {
            return response()->json(['message' => 'Ya tienes este libro'], 409);
        }

        return response()->json(['message' => $message, 'book' => $book], 201);
    }
}