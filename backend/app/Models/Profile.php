<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * (Deben coincidir con los campos de tu migración)
     */
    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'telefono',
    ];

    /**
     * Relación inversa: Un perfil pertenece a un único usuario (1:1).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}