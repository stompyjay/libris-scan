<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'author', // Mantenemos esto por compatibilidad visual rápida, aunque la relación real estará en author_book
        'cover',
        'description',
        'isbn',
        'price',
    ];

    // 1. RELACIÓN CON CATEGORÍA
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 2. RELACIÓN CON AUTORES (NUEVO E IMPORTANTE)
    // Esto permite usar $book->authors para ver los datos de la tabla authors
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book');
    }

    // 3. RELACIÓN CON USUARIOS
    public function users()
    {
        return $this->belongsToMany(User::class, 'book_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}