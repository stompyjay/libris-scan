<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Book; 
use App\Models\Author; // <--- ¡IMPORTANTE! Faltaba importar el modelo Author

class BookSearchController extends Controller
{
    /**
     * API: Buscar libros en OpenLibrary
     * Método: GET /api/scan?query=Harry+Potter
     */
    public function index(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'Por favor escribe algo para buscar.'], 400);
        }
        
        // 1. Preguntamos a Internet (Open Library)
        $response = Http::get("https://openlibrary.org/search.json?q=" . $query . "&limit=10");
        $data = $response->json();

        // 2. Procesamos los resultados
        $books = [];
        if (!empty($data['docs'])) {
            foreach ($data['docs'] as $doc) {
                $books[] = [
                    'title'           => $doc['title'] ?? 'Sin título',
                    'author'          => $doc['author_name'][0] ?? 'Desconocido', // Esto solo viaja al frontend, no se guarda aun
                    'year'            => $doc['first_publish_year'] ?? null,
                    'cover_id'        => $doc['cover_i'] ?? null,
                    'suggested_category' => $doc['subject'][0] ?? 'General'
                ];
            }
        }

        return response()->json($books);
    }

    /**
     * API: Guardar un libro seleccionado del escáner
     * Método: POST /api/scan
     */
    public function search(Request $request) 
    {
        // 1. Validamos
        $request->validate([
            'title' => 'required|string',
            'author' => 'nullable|string', // Recibimos el nombre del autor como texto
            'suggested_category' => 'nullable|string'
        ]);

        // 2. Lógica de Categoría (First or Create)
        $categoryName = $request->input('suggested_category', 'General');
        $category = Category::firstOrCreate(
            ['name' => $categoryName], 
            ['description' => 'Categoría importada automáticamente'] 
        );

        // 3. Crear el libro (SIN el campo author)
        $book = Book::create([
            'title'       => $request->input('title'),
            // 'author'   => ... ¡ELIMINADO! Ya no existe esa columna
            'category_id' => $category->id,
            'description' => 'Importado desde OpenLibrary',
            'status'      => 'pending',
            'user_id'     => Auth::id(),
        ]);

        // 4. Lógica de AUTOR (La parte nueva N:M)
        $authorName = $request->input('author', 'Desconocido');

        // Buscamos si el autor existe por nombre, si no, lo creamos
        $author = Author::firstOrCreate(
            ['name' => $authorName],
            ['bio' => 'Autor importado de OpenLibrary'] // Dato opcional si se crea nuevo
        );

        // Vinculamos el libro con el autor
        $book->authors()->attach($author->id);

        return response()->json([
            'message' => '¡Libro guardado con éxito!',
            'category_created' => $category->wasRecentlyCreated,
            'book' => $book->load('authors') // Devolvemos el libro con el autor cargado
        ], 201);
    }

    /**
     * API: Explorar por Categoría
     */
    public function browseByCategory(Request $request)
    {
        $category = $request->input('category'); 

        if (!$category) {
            return response()->json(['error' => 'Falta la categoría'], 400);
        }

        $response = Http::get("https://openlibrary.org/subjects/" . strtolower($category) . ".json?limit=12");
        $data = $response->json();

        $books = [];

        if (isset($data['works'])) {
            foreach ($data['works'] as $work) {
                $books[] = [
                    'title'      => $work['title'],
                    'author'     => $work['authors'][0]['name'] ?? 'Desconocido',
                    'cover_id'   => $work['cover_id'] ?? null,
                    'suggested_category' => ucfirst($category) 
                ];
            }
        }

        return response()->json($books);
    }
}