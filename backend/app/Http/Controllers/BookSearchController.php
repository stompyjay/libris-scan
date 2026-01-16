<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use App\Models\Category;
use App\Models\Book; // <--- Asegúrate de tener un modelo Book o comenta esto

class BookSearchController extends Controller
{
    // FUNCIÓN 1: Mostrar la pantalla
    public function index()
    {
        return view('books.search');
    }

    // FUNCIÓN 2: Buscar por texto y guardar
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // 1. Preguntamos a Internet (Open Library)
        $response = Http::get("https://openlibrary.org/search.json?q=" . $query);
        $data = $response->json();

        // 2. Si encontramos el libro...
        if (!empty($data['docs'])) {
            $bookData = $data['docs'][0];
            
            // 3. Decidimos la categoría automática
            $categoryName = 'General';
            if (isset($bookData['subject']) && count($bookData['subject']) > 0) {
                $categoryName = $bookData['subject'][0]; 
            }

            // 4. Creamos la categoría si no existe
            $category = Category::firstOrCreate(
                ['name' => $categoryName], 
                ['description' => 'Categoría automática'] 
            );

            // AQUÍ GUARDARÍAMOS EL LIBRO (Descomenta cuando tengas el modelo Book)
            /*
            Book::create([
                'title' => $bookData['title'],
                'author' => $bookData['author_name'][0] ?? 'Desconocido',
                'category_id' => $category->id,
                'user_id' => auth()->id(),
            ]);
            */

            return redirect()->route('dashboard')->with('success', '¡Libro detectado! Categoría creada: ' . $categoryName);
        }

        return back()->with('error', 'No hemos encontrado ese libro. Prueba con otro nombre.');
    }

    // FUNCIÓN 3: Explorar por Categoría (Esta debe ir fuera de search)
    public function browseByCategory(Request $request)
    {
        $category = $request->input('category'); // Ej: 'fantasy', 'romance'

        // Si no han elegido nada, volvemos atrás
        if (!$category) {
            return back();
        }

        // Consultamos la API de Temas (Subjects)
        $response = Http::get("https://openlibrary.org/subjects/" . strtolower($category) . ".json?limit=12");
        $data = $response->json();

        $books = [];

        // Procesamos los resultados para que la vista los entienda
        if (isset($data['works'])) {
            foreach ($data['works'] as $work) {
                $books[] = [
                    'title' => $work['title'],
                    'author' => $work['authors'][0]['name'] ?? 'Desconocido',
                    'cover_id' => $work['cover_id'] ?? null,
                    // Guardamos el subject original para usarlo si deciden guardar el libro
                    'subject' => $category 
                ];
            }
        }

        // Reutilizamos la vista de búsqueda pero le pasamos los libros encontrados
        return view('books.search', [
            'books' => $books, 
            'selectedCategory' => ucfirst($category)
        ]);
    }
}