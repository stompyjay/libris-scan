<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use App\Models\User; // <--- ESTO FALTABA PARA QUE FUNCIONE LA REGLA DE VALIDACIÓN

class ProfileController extends Controller
{
    /**
     * Muestra el perfil del usuario (GET)
     */
    public function index(Request $request)
    {
        return $request->user();
    }

    /**
     * Actualiza el perfil del usuario (PUT)
     */
    public function update(ProfileUpdateRequest $request) // <--- HE QUITADO ": RedirectResponse" PARA EVITAR EL ERROR 500
    {
        // 1. VALIDACIÓN
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'], 
            'phone' => ['required', 'string', 'max:20'],    
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
        ]);

        // 2. RELLENAR EL USUARIO
        $request->user()->fill($validated);

        // Si el email cambió, reseteamos la verificación
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // 3. GUARDAR EN BASE DE DATOS
        $request->user()->save();

        // 4. RESPONDER AL JAVASCRIPT (JSON) O REDIRIGIR
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $request->user(),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}