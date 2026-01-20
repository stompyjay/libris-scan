<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * API: Obtener datos del perfil
     * URL: GET /api/profile
     */
    public function index()
    {
        $user = Auth::user();

        // Si no tiene perfil, lo creamos vacío al vuelo
        $profile = $user->profile ?: $user->profile()->create([
            'nombre' => '',
            'apellido' => '',
            'telefono' => '',
        ]);

        // Devolvemos JSON para que tu profile.html lo lea
        return response()->json($profile);
    }

    /**
     * API: Actualizar perfil
     * URL: PUT /api/profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // 1. Validar
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ]);

        // 2. Actualizar
        $profile->update($validated);

        // 3. Responder con JSON (mensaje de éxito)
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'profile' => $profile
        ]);
    }
}