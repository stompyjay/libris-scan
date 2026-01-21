<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Muestra el perfil del usuario (GET)
     */
    public function index(Request $request)
    {
        // ESTA es la línea mágica que te faltaba.
        // Devuelve al usuario logueado en formato JSON.
        return $request->user();
    }

    /**
     * Actualiza el perfil del usuario (PUT)
     */
    public function update(Request $request)
    {
        // 1. Validamos los datos que llegan del formulario
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // El email es único, pero ignoramos el id del usuario actual para que no de error si no lo cambia
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'apellido' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        // 2. Actualizamos el usuario
        $request->user()->fill($validated);

        // 3. Si el email cambió, invalidamos la verificación (opcional, buena práctica)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // 4. Devolvemos respuesta de éxito
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $request->user(),
        ]);
    }
}