<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra la lista de categorías (ej. tabla en el admin).
     */
    public function index()
    {
        // Obtenemos todas las categorías, paginadas de 10 en 10
        $categories = Category::orderBy('name', 'asc')->paginate(10);
        
        // Retornamos la vista (tendrás que crear resources/views/categories/index.blade.php)
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario vacío para crear una categoría manual.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * Recibe los datos del formulario y guarda en BBDD.
     */
    public function store(Request $request)
    {
        // 1. Validación: El nombre es obligatorio y único
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        // 2. Crear la categoría usando asignación masiva
        Category::create($validated);

        // 3. Redireccionar con mensaje de éxito
        return redirect()->route('categories.index')
                         ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     * Muestra una categoría específica (y quizás sus libros asociados).
     */
    public function show(Category $category) // Laravel busca automáticamente por ID
    {
        // Opcional: Cargar los libros de esta categoría
        // $books = $category->books;
        
        // return view('categories.show', compact('category'));
        
        // POR AHORA: Lo dejamos vacío o redirigimos, según tu necesidad
        return "Aquí se mostrarían los detalles de la categoría: " . $category->name;
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario con los datos cargados para editar.
     */
    public function edit(Category $category)
    {
        // Pasamos la categoría existente a la vista de edición
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * Guarda los cambios de la edición.
     */
    public function update(Request $request, Category $category)
    {
        // 1. Validación
        $validated = $request->validate([
            // 'unique' ignora el ID actual para que no de error si no cambias el nombre
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        // 2. Actualizar
        $category->update($validated);

        // 3. Redireccionar
        return redirect()->route('categories.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Elimina la categoría de la base de datos.
     */
    public function destroy(Category $category)
    {
        // Opcional: Verificar si tiene libros antes de borrar para no dejar huérfanos
        /*
        if ($category->books()->count() > 0) {
            return back()->with('error', 'No puedes borrar una categoría con libros asociados.');
        }
        */

        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Categoría eliminada.');
    }
}