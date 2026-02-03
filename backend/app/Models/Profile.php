<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // IMPORTANTE: Estos deben coincidir con las columnas de tu migración
    protected $fillable = ['user_id', 'nombre', 'surname', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}