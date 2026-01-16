<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Recomendado tenerlo

class Category extends Model
{
    use HasFactory;

    // Permitimos que 'name' y 'description' se llenen masivamente
    // Esto es OBLIGATORIO para usar Category::firstOrCreate(...)
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relación: Una categoría tiene muchos libros.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}