<?php

namespace App\Http\Controllers;

use App\Models\Review; // Asegúrate de tener el modelo creado
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * API: Obtener reseñas para la Landing Page
     * Es PÚBLICA (no requiere login)
     */
    public function index()
{
    // Solo obtenemos las que tengan is_visible = true (o 1)
    $reviews = Review::where('is_visible', true)
                     ->latest()
                     ->take(3)
                     ->get();

    return response()->json($reviews);
}
}