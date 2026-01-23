<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book; // <--- ¡MUY IMPORTANTE! Sin esto da Error 500

class PurchaseController extends Controller
{
    // 1. FUNCIÓN PARA VER LOS LIBROS (GET)
    public function index(Request $request)
    {
        // Busca los libros del usuario
        $books = Book::where('user_id', $request->user()->id)->get();
        
        // Devuelve la lista en JSON (Evita el error JSON.parse)
        return response()->json($books);
    }

    // 2. FUNCIÓN PARA COMPRAR/GUARDAR (POST)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            // 'author' y 'cover' son opcionales, así que no es estricto
        ]);

        $book = new Book();
        $book->user_id = $request->user()->id;
        $book->title = $request->title;
        $book->author = $request->author ?? 'Desconocido';
        $book->cover_id = $request->cover; // Ojo: asegúrate que tu JS envía 'cover'
        $book->isbn = $request->isbn;      // Guardamos el ISBN
        $book->status = 'pending';
        
        $book->save();

        return response()->json(['message' => 'Libro guardado con éxito'], 201);
    }
}