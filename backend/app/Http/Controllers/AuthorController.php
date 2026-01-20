<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * API: Obtener mi perfil
     * Método: GET /api/mi-perfil
     * Lógica: Si no existe, lo crea vacío.
     */
    public function index()
    {
        $user = Auth::user();

        // Intenta obtener el perfil. Si es null, crea uno nuevo vacío.
        $profile = $user->profile ?: $user->profile()->create([
            'nombre'   => '',
            'apellido' => '',
            'telefono' => '',
        ]);

        return response()->json($profile);
    }

    /**
     * ELIMINADO: public function edit(Profile $profile)
     * Las APIs no devuelven formularios.
     */

    /**
     * API: Actualizar perfil
     * Método: PUT /api/mi-perfil/{id}
     */
    public function update(Request $request, Profile $profile)
    {
        // 1. SEGURIDAD: Verificar que el perfil pertenece al usuario logueado
        if ($profile->user_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para editar este perfil.'], 403);
        }

        // 2. Validar datos
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ]);

        // 3. Actualizar
        $profile->update($validated);

        // 4. Respuesta JSON
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'profile' => $profile
        ]);
    }

    /**
     * --- MÉTODOS EXTRA (Si los necesitas por requisitos académicos) ---
     * En una API real RESTful, estos se usarían, pero en tu flujo 
     * de "Mi Perfil" actual no son necesarios.
     */

    public function create()
    {
        return response()->json(['message' => 'No implementado en API'], 501);
    }

    public function store(Request $request)
    {
        // La creación se maneja automáticamente en el index(), pero si quisieras hacerlo manual:
        /*
        $profile = Auth::user()->profile()->create($request->all());
        return response()->json($profile, 201);
        */
        return response()->json(['message' => 'Usar index para auto-creación'], 200);
    }

    public function show(Profile $profile)
    {
        // Seguridad
        if ($profile->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json($profile);
    }

    public function destroy(Profile $profile)
    {
        // Por si decides permitir borrar perfil
        if ($profile->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $profile->delete();
        return response()->json(null, 204);
    }
}