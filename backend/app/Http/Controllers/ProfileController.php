<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Muestra los datos del perfil (Relación 1:1).
     * MEJORA: Si el perfil no existe, lo crea vacío para evitar errores.
     */
    public function index()
    {
        // Intenta obtener el perfil. Si es null (no existe), crea uno nuevo vacío.
        $profile = Auth::user()->profile ?: Auth::user()->profile()->create([
            'nombre' => '',
            'apellido' => '',
            'telefono' => '',
        ]);

        return view('user-profile.index', compact('profile'));
    }

    /**
     * Formulario de edición (Requisito B.1).
     */
    public function edit(Profile $profile)
    {
        // SEGURIDAD: Verifica que el usuario solo edite SU propio perfil
        if ($profile->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este perfil.');
        }

        return view('user-profile.edit', compact('profile'));
    }

    /**
     * Actualiza Nombre, Apellido y Teléfono (Requisito B.1).
     */
    public function update(Request $request, Profile $profile)
    {
        // 1. Validar datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
        ]);

        // 2. SEGURIDAD: Verificar propiedad de nuevo antes de guardar
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }

        // 3. Actualizar
        $profile->update($request->only(['nombre', 'apellido', 'telefono']));

        // 4. Redirigir (Asegúrate de que esta ruta existe en web.php)
        return redirect()->route('profile.index')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * --- MÉTODOS REQUERIDOS POR PD1 (Aunque no se usen, deben existir) ---
     */

    public function create()
    {
        // PD1: Método existente vacío
    }

    public function store(Request $request)
    {
        // PD1: Método existente vacío
    }

    public function show(Profile $profile)
    {
        // PD1: Método existente vacío
    }

    public function destroy(Profile $profile)
    {
        // PD1: Método existente vacío
    }
}