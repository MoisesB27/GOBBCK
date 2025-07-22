<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario utiliza un token válido o sesión, se autentica, si no, sigue sin interrumpir
        if (Auth::check()) {
            // Usuario autenticado, sigue normalmente
            return $next($request);
        }

        // Usuario no autenticado, permite continuar
        return $next($request);
    }
}
