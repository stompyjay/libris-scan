<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'category_id', 
        'title', 
        'isbn', 
        'cover_id', 
        'description', 
        'status'
    ];

    // Relación 1:N Inversa (Un libro pertenece a UN usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación 1:N Inversa (Un libro pertenece a UNA categoría)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // --- AQUÍ ESTÁ EL REQUISITO N:M ---
    // Relación N:M (Un libro pertenece a MUCHOS autores)
    public function authors()
    {
        // Laravel busca automáticamente la tabla pivote 'author_book'
        return $this->belongsToMany(Author::class);
    }
}