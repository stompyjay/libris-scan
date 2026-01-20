<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relación 1:N (Una categoría tiene MUCHOS libros)
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}