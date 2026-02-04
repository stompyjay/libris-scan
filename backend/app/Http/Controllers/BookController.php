<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author; // <--- IMPORTANTE: No olvides importar esto
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    /**
     * API: BÚSQUEDA INTELIGENTE (OpenLibrary)
     * (Sin cambios, solo devuelve datos planos para el frontend)
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
                        // Aquí enviamos el autor como string, luego en purchase lo convertiremos a ID
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
     * API: LISTAR MIS LIBROS
     */
    public function myBooks(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            $user = User::first();
        }

        if (!$user) {
            return response()->json([]); 
        }

        try {
            $books = $user->books()
                          ->with('category')
                          ->with('authors') // <--- NUEVO: Cargar la relación de autores
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
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'books' => 'required|array',
        ]);
        
        $user = $request->user();
        if (!$user) $user = User::first();

        $purchasedBooks = [];

        foreach ($request->books as $item) {
            // 1. Datos básicos
            $titulo = !empty($item['title']) ? $item['title'] : 'Libro sin título';
            // Recibimos el nombre del autor (string)
            $authorName = !empty($item['author']) ? $item['author'] : 'Desconocido';
            
            // 2. Gestionar CATEGORÍA
            $catName = $item['suggested_category'] ?? 'General';
            $category = Category::firstOrCreate(['name' => ucfirst($catName)]);

            // 3. Gestionar AUTOR (NUEVO LÓGICA)
            // Busca si existe un autor con ese nombre, si no, lo crea en la tabla 'authors'
            $authorModel = Author::firstOrCreate([
                'name' => $authorName
            ]);

            // 4. Buscar o Crear el LIBRO
            $book = Book::updateOrCreate(
                [
                    'title' => $titulo 
                ],
                [
                    // Guardamos el string también por si acaso (backup), 
                    // pero la relación importante es la de abajo.
                    'author'      => $authorName, 
                    'isbn'        => $item['isbn'] ?? null,
                    'cover'       => $item['cover'] ?? 'https://via.placeholder.com/150',
                    'price'       => $item['price'] ?? 10.00,
                    'category_id' => $category->id,
                    'description' => 'Comprado en la tienda',
                ]
            );

            // 5. VINCULAR LIBRO CON AUTOR (NUEVO)
            // Llenamos la tabla 'author_book'
            // syncWithoutDetaching evita borrar otros autores si el libro ya existía y tenía varios
            $book->authors()->syncWithoutDetaching([$authorModel->id]);

            // 6. VINCULAR LIBRO CON USUARIO (Compra)
            $user->books()->syncWithoutDetaching([
                $book->id => ['status' => 'pending']
            ]);

            // Cargar la relación para devolver el objeto completo
            $book->load('authors');
            $purchasedBooks[] = $book;
        }

        return response()->json([
            'message' => 'Compra realizada con éxito', 
            'books' => $purchasedBooks
        ], 200);
    }

    /**
     * API: GUARDAR UN SOLO LIBRO (Manual)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $user = $request->user() ?? User::first();

        // 1. Gestionar Categoría
        $categoryName = $request->suggested_category ?? 'General';
        $category = Category::firstOrCreate(['name' => ucfirst($categoryName)]);

        // 2. Gestionar Autor (NUEVO)
        $authorName = $request->author ?? 'Desconocido';
        $authorModel = Author::firstOrCreate(['name' => $authorName]);

        // 3. Crear el libro
        // Nota: Quitamos user_id del create del libro porque la relación es N:M en book_user
        $book = Book::firstOrCreate(
            [
                'title' => $request->title,
            ], 
            [
                'author' => $authorName, // String backup
                'isbn' => $request->isbn,
                'cover' => $request->cover ?? 'https://via.placeholder.com/150',
                'category_id' => $category->id,
                'price' => 12.50,
                'description' => 'Añadido manualmente'
            ]
        );

        // 4. Vincular Autor (Tabla author_book)
        $book->authors()->syncWithoutDetaching([$authorModel->id]);

        // 5. Vincular al usuario (Tabla book_user)
        if (!$user->books()->where('book_id', $book->id)->exists()) {
            $user->books()->attach($book->id, ['status' => 'pending']);
            $message = 'Libro añadido a tu biblioteca';
        } else {
            return response()->json(['message' => 'Ya tienes este libro'], 409);
        }

        // Devolvemos el libro con su autor cargado
        $book->load('authors');

        return response()->json(['message' => $message, 'book' => $book], 201);
    }

    // ... Destroy, Show y UpdateStatus
    
    public function destroy(Request $request, $id)
    {
        $user = $request->user() ?? User::first();
        $detached = $user->books()->detach($id);
        if ($detached) return response()->json(['message' => 'Libro eliminado']);
        return response()->json(['error' => 'No encontrado'], 404);
    }
    
    public function show(Request $request, $id)
    {
        $user = $request->user() ?? User::first();
        // Cargamos también 'authors' aquí
        $book = $user->books()
                     ->with(['category', 'authors']) 
                     ->withPivot('status')
                     ->find($id);

        if (!$book) return response()->json(['error' => 'No encontrado'], 404);
        $book->status = $book->pivot->status;
        return response()->json($book);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        $user = $request->user() ?? User::first();
        $user->books()->updateExistingPivot($id, ['status' => $request->status]);
        return response()->json(['message' => 'Estado actualizado']);
    }
}