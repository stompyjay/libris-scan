<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nombre', 'apellido', 'telefono'];

    // Relación 1:1 Inversa (El perfil pertenece a UN usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}