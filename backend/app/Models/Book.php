<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // 1. CAMPOS PERMITIDOS (Seguridad)
    // Esto es OBLIGATORIO para usar Book::create() en el controlador
    protected $fillable = [
        'title',
        'author',
        'cover_id',
        'description',
        'status',
        'user_id',
        'category_id',
    ];

    // 2. RELACIONES
    
    // Un libro pertenece a un Usuario (el dueño)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un libro pertenece a una Categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}