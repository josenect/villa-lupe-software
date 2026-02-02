<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles  Roles permitidos
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Si no está autenticado, redirigir a login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder.');
        }

        $user = Auth::user();

        // Si el usuario no está activo
        if (!$user->activo) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Su cuenta ha sido desactivada.');
        }

        // Si no se especifican roles, permitir a cualquier usuario autenticado
        if (empty($roles)) {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles permitidos
        if (in_array($user->rol, $roles)) {
            return $next($request);
        }

        // Si no tiene permiso, redirigir según su rol
        if ($user->esCocina()) {
            return redirect()->route('cocina.index')->with('error', 'No tiene permiso para acceder a esta sección.');
        }

        return redirect()->route('inicio')->with('error', 'No tiene permiso para acceder a esta sección.');
    }
}
