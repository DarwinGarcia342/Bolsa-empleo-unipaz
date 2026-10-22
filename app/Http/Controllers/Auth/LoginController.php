<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Verificar si está activo
            if (!$user->active) {
                Auth::logout();
                return back()->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
            }

            $request->session()->regenerate();

            $url = match ($user->role) {
                'admin'   => route('admin.dashboard'),
                'company' => route('company.dashboard'),
                'student' => route('student.dashboard'),
                default   => '/',
            };

            return redirect()->intended(trim($url));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente.');
    }
}
