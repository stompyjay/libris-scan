<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book; 

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        try {
            // 1. Identificar al usuario (puede ser el logueado o crear uno test si está vacío)
            $user = User::first(); // Por ahora usamos el primero para probar rápido
            
            // Si no hay usuarios (porque hicimos migrate:fresh), creamos uno
            if (!$user) {
                $user = User::create([
                    'name' => 'Usuario Test',
                    'surname' => 'Apellido',
                    'email' => 'test@test.com',
                    'password' => bcrypt('12345678'),
                    'phone' => '123456789'
                ]);
            }

            // 2. Buscar el libro que se quiere comprar
            // Buscamos por título (o idealmente por ID si el frontend lo enviara)
            $book = Book::where('title', $request->title)->first();

            // Si el libro no existe en la BD, lo creamos al vuelo (para que no falle la prueba)
            if (!$book) {
                $book = Book::create([
                    'title' => $request->title,
                    'description' => 'Descripción automática',
                    'price' => 10.00,
                    'cover' => 'https://via.placeholder.com/150' // Imagen por defecto
                ]);
            }

            // 3. ¡LA MAGIA! Guardar la relación en la tabla 'book_user'
            // El método syncWithoutDetaching evita duplicados (no compras el mismo libro 2 veces)
            $user->books()->syncWithoutDetaching([
                $book->id => ['status' => 'pending'] // Rellenamos la columna status
            ]);
            
            return response()->json([
                'message' => 'Compra realizada y guardada',
                'book_title' => $book->title
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}