<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    // --- SCAN (API OPENLIBRARY) ---
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

    // --- MY BOOKS (LISTAR) ---
    public function myBooks(Request $request)
    {
        $user = $request->user(); // SOLO el usuario autenticado

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        try {
            $books = $user->books()
                          ->with(['category', 'authors']) // Cargar relaciones
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

    // --- PURCHASE (COMPRAR) ---
    public function purchase(Request $request)
    {
        $request->validate(['books' => 'required|array']);
        
        $user = $request->user(); // SOLO usuario autenticado

        if (!$user) {
            return response()->json(['message' => 'Debes iniciar sesión para comprar'], 401);
        }

        $purchasedBooks = [];

        foreach ($request->books as $item) {
            $titulo = !empty($item['title']) ? $item['title'] : 'Libro sin título';
            $authorName = !empty($item['author']) ? $item['author'] : 'Desconocido';
            
            // Categoría
            $catName = $item['suggested_category'] ?? 'General';
            $category = Category::firstOrCreate(['name' => ucfirst($catName)]);

            // Autor
            $authorModel = Author::firstOrCreate(['name' => $authorName]);

            // Libro
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

            // Relaciones
            $book->authors()->syncWithoutDetaching([$authorModel->id]);
            $user->books()->syncWithoutDetaching([$book->id => ['status' => 'pending']]);

            $book->load('authors');
            $purchasedBooks[] = $book;
        }

        return response()->json([
            'message' => 'Compra realizada con éxito', 
            'books' => $purchasedBooks
        ], 200);
    }

    // --- STORE (GUARDAR MANUAL) ---
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string']);

        $user = $request->user(); // <--- CORREGIDO: Eliminado "?? User::first()"
        
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

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
            $message = 'Libro añadido a tu biblioteca';
        } else {
            return response()->json(['message' => 'Ya tienes este libro'], 409);
        }

        $book->load('authors');
        return response()->json(['message' => $message, 'book' => $book], 201);
    }

    // --- DESTROY (ELIMINAR) ---
    public function destroy(Request $request, $id)
    {
        $user = $request->user(); // <--- CORREGIDO
        if (!$user) return response()->json(['error' => 'No autorizado'], 401);

        $detached = $user->books()->detach($id);
        
        if ($detached) return response()->json(['message' => 'Libro eliminado']);
        return response()->json(['error' => 'No encontrado'], 404);
    }
    
    // --- SHOW (VER UNO) ---
    public function show(Request $request, $id)
    {
        $user = $request->user(); // <--- CORREGIDO
        if (!$user) return response()->json(['error' => 'No autorizado'], 401);

        $book = $user->books()
                     ->with(['category', 'authors']) 
                     ->withPivot('status')
                     ->find($id);

        if (!$book) return response()->json(['error' => 'No encontrado'], 404);
        
        $book->status = $book->pivot->status;
        return response()->json($book);
    }

    // --- UPDATE STATUS ---
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        
        $user = $request->user(); // <--- CORREGIDO
        if (!$user) return response()->json(['error' => 'No autorizado'], 401);

        $user->books()->updateExistingPivot($id, ['status' => $request->status]);
        return response()->json(['message' => 'Estado actualizado']);
    }
}