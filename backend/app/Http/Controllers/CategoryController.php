<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * API: Listar categorías
     * Método: GET /api/categorias
     */
    public function index()
    {
        // Devolvemos las categorías paginadas en formato JSON
        // Nota: Si quieres todas sin paginar para un select, usa Category::all()
        $categories = Category::orderBy('name', 'asc')->get(); 
        
        return response()->json($categories);
    }

    /**
     * ELIMINADOS: create() y edit()
     * No necesitamos pintar formularios HTML desde Laravel.
     */

    /**
     * API: Crear categoría
     * Método: POST /api/categorias
     */
    public function store(Request $request)
    {
        // 1. Validación
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        // 2. Crear
        $category = Category::create($validated);

        // 3. Respuesta JSON (201 Created)
        return response()->json([
            'message'  => 'Categoría creada correctamente',
            'category' => $category
        ], 201);
    }

    /**
     * API: Ver una categoría
     * Método: GET /api/categorias/{id}
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * API: Actualizar categoría
     * Método: PUT /api/categorias/{id}
     */
    public function update(Request $request, Category $category)
    {
        // 1. Validación
        $validated = $request->validate([
            // Ignoramos el ID actual en la validación unique para que no de error consigo mismo
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        // 2. Actualizar
        $category->update($validated);

        // 3. Respuesta JSON
        return response()->json([
            'message'  => 'Categoría actualizada',
            'category' => $category
        ]);
    }

    /**
     * API: Eliminar categoría
     * Método: DELETE /api/categorias/{id}
     */
    public function destroy(Category $category)
    {
        // Opcional: Validación extra
        // Si tienes la relación definida en el modelo, podrías descomentar esto:
        /*
        if ($category->books()->count() > 0) {
             return response()->json(['error' => 'No puedes borrar una categoría con libros.'], 409);
        }
        */

        $category->delete();

        // 204 No Content
        return response()->json(null, 204);
    }
}