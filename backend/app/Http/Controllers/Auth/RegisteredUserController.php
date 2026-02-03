<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * ESTA ES LA FUNCIÓN QUE TE FALTABA
     */
    public function create(): View
    {
        // Esto le dice a Laravel: "Muestra el archivo resources/views/auth/register.blade.php"
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * TU LÓGICA ORIGINAL SE MANTIENE AQUÍ
     */
   public function store(Request $request): RedirectResponse
    {
        // ... validaciones ...

        // 1. Crear Usuario
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. OBLIGATORIO: Crear el Perfil inicial
        // Si no haces esto, updateOrCreate funcionará, pero es mejor tenerlo desde el inicio
        $user->profile()->create([
            'nombre'  => $request->name,
            'surname' => $request->surname,
            'phone'   => $request->phone,
        ]);



        event(new Registered($user));

        Auth::login($user);

        // CAMBIO: Redirección directa al archivo HTML del frontend
        return redirect('/dashboard.html');
    }
}