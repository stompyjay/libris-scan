<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        // Validamos que nos llegue la info básica del libro
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'cover_image' => 'nullable'
        ]);

        // "Comprar" el libro = Crear una copia en TU base de datos personal
        $book = Book::create([
            'user_id' => $request->user()->id, // Asignado a TI
            'title' => $request->title,
            'author' => $request->author,
            'cover_image' => $request->cover_image,
            'status' => 'pending', // Por defecto "Pendiente de leer"
            'category_id' => 1, // Puedes ajustar esto o enviarlo desde el front
        ]);

        return response()->json(['message' => 'Libro comprado con éxito', 'book' => $book]);
    }
}