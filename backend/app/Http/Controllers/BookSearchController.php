<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Book; 

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

        // 2. Procesamos los resultados para limpiarlos antes de enviarlos al Frontend
        $books = [];
        if (!empty($data['docs'])) {
            foreach ($data['docs'] as $doc) {
                // Filtramos para que solo lleguen datos útiles
                $books[] = [
                    'title'       => $doc['title'] ?? 'Sin título',
                    'author'      => $doc['author_name'][0] ?? 'Desconocido',
                    'year'        => $doc['first_publish_year'] ?? null,
                    'cover_id'    => $doc['cover_i'] ?? null, // ID para buscar la portada luego
                    // Sugerimos una categoría basada en el primer "subject"
                    'suggested_category' => $doc['subject'][0] ?? 'General'
                ];
            }
        }

        return response()->json($books);
    }

    /**
     * API: Guardar un libro seleccionado del escáner
     * Método: POST /api/scan
     * Este método hace la MAGIA de crear la categoría si no existe.
     */
    public function search(Request $request) // Nota: En tus rutas lo llamaste 'search', aquí actúa como guardar
    {
        // 1. Validamos los datos que nos envía el Frontend
        $request->validate([
            'title' => 'required|string',
            'author' => 'nullable|string',
            'suggested_category' => 'nullable|string'
        ]);

        // 2. Lógica de Categoría Automática
        $categoryName = $request->input('suggested_category', 'General');
        
        // Buscamos la categoría, si no existe, la creamos al vuelo
        $category = Category::firstOrCreate(
            ['name' => $categoryName], 
            ['description' => 'Categoría importada automáticamente'] 
        );

        // 3. Guardamos el libro
        $book = Book::create([
            'title'       => $request->input('title'),
            'author'      => $request->input('author'),
            'category_id' => $category->id, // Usamos el ID de la categoría (nueva o existente)
            'description' => 'Importado desde OpenLibrary',
            'status'      => 'pending', // Por defecto 'pendiente'
            'user_id'     => Auth::id(),
        ]);

        return response()->json([
            'message' => '¡Libro guardado con éxito!',
            'category_created' => $category->wasRecentlyCreated, // Booleano para saber si se creó nueva
            'book' => $book
        ], 201);
    }

    /**
     * API: Explorar por Categoría (OpenLibrary Subjects)
     * Método: GET /api/browse?category=fantasy
     */
    public function browseByCategory(Request $request)
    {
        $category = $request->input('category'); 

        if (!$category) {
            return response()->json(['error' => 'Falta la categoría'], 400);
        }

        // Consultamos la API de Temas
        $response = Http::get("https://openlibrary.org/subjects/" . strtolower($category) . ".json?limit=12");
        $data = $response->json();

        $books = [];

        if (isset($data['works'])) {
            foreach ($data['works'] as $work) {
                $books[] = [
                    'title'     => $work['title'],
                    'author'    => $work['authors'][0]['name'] ?? 'Desconocido',
                    'cover_id'  => $work['cover_id'] ?? null,
                    'suggested_category' => ucfirst($category) 
                ];
            }
        }

        return response()->json($books);
    }
}