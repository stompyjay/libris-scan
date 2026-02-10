<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
{
    // 1. Verificamos si está logueado
    if (!auth()->check()) {
        return response()->json(['message' => 'No estás logueado'], 401);
    }

    // 2. Verificamos si es ADMIN (usamos la columna 'admin' que creaste)
    if (!auth()->user()->admin) {
        return response()->json(['message' => 'Acceso denegado. No eres admin.'], 403);
    }

    return $next($request);
}
