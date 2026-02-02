<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verificar si está activo
            if (!$user->activo) {
                Auth::logout();
                return back()->with('error', 'Su cuenta ha sido desactivada. Contacte al administrador.');
            }

            $request->session()->regenerate();
            return $this->redirectByRole($user);
        }

        return back()->with('error', 'Credenciales incorrectas.');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Redirigir según el rol del usuario
     */
    private function redirectByRole($user)
    {
        if ($user->esCocina()) {
            return redirect()->route('cocina.index');
        }
        return redirect()->route('inicio');
    }
}
