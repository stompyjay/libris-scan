<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        // 1. VALIDACIÓN
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users')->ignore($request->user()->id)
            ],
        ]);

        $user = $request->user();

        // --- AQUÍ ESTABA EL ERROR ---
        // 2. ACTUALIZAR TABLA USERS
        // Faltaba añadir surname y phone aquí para que se guarden en la tabla 'users'
        $user->fill([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'surname' => $validated['surname'], // <--- AGREGADO
            'phone'   => $validated['phone'],   // <--- AGREGADO
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save(); // Ahora sí guarda el teléfono y apellido en 'users'

        // 3. ACTUALIZAR TABLA PROFILES
        // Esto estaba bien, mantiene la copia en la otra tabla
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nombre'  => $validated['name'],
                'surname' => $validated['surname'],
                'phone'   => $validated['phone'],
            ]
        );

        return back()->with('status', 'Perfil actualizado correctamente');
    }
}