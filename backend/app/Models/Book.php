<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Campos que permitimos rellenar masivamente
    protected $fillable = [
        'title', 
        'author', 
        'category_id', 
        'cover_id', 
        'cover',    // <--- AÑADE ESTO (para la URL de la imagen)
        'isbn',     // <--- AÑADE ESTO
        'price'     // <--- AÑADE ESTO (Ya lo tenías, pero revisa)
    ];

    // 1. RELACIÓN CON CATEGORÍA (Esta es la que faltaba y causaba el error)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 2. RELACIÓN CON USUARIOS (Para saber quién compró el libro)
    public function users()
    {
        return $this->belongsToMany(User::class, 'book_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}