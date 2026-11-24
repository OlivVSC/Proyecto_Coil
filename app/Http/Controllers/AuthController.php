<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function formularioLogin() { return view('login'); }
    public function formularioRegistro() { return view('registro'); }

    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'rol' => ['required', 'in:usuario,admin'],
        ]);
        // El rol forma parte de la verificación, no basta con escogerlo en pantalla.
        if (!Auth::attempt(['email' => $datos['email'], 'password' => $datos['password'], 'rol' => $datos['rol']])) {
            return back()->withErrors(['email' => 'Revisa el correo, la contraseña y el tipo de cuenta.'])->onlyInput('email', 'rol');
        }
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    public function registro(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => 'usuario', // Nadie puede registrarse públicamente como admin.
        ]);
        return redirect()->route('login')->with('status', 'Cuenta creada. Ya puedes iniciar sesión.');
    }

    public function salir(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
