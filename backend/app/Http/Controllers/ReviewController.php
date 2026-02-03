<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Método para OBTENER las reseñas
    public function index()
    {
        $reviews = Review::latest()->take(6)->get();
        return response()->json($reviews);
    }

    // Método para GUARDAR una reseña
    // (Este método debe estar DENTRO de las llaves de la clase)
    public function store(Request $request)
    {
        // 1. Validamos
        $validated = $request->validate([
            'user_name' => 'required|string|max:50',
            'rating'    => 'required|integer|min:1|max:5',
            'content'   => 'required|string|max:500',
        ]);

        // 2. Creamos
        $review = Review::create($validated);

        // 3. Respondemos
        return response()->json($review, 201);
    }
} // <--- ¡LA LLAVE DE CIERRE VA AQUÍ AL FINAL!