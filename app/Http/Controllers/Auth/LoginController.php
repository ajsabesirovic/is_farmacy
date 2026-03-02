<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Korisnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $korisnik = Korisnik::where('email', $request->email)->first();

        if (!$korisnik || !Hash::check($request->password, $korisnik->lozinka_hash)) {
            return back()->withErrors([
                'email' => 'Pogrešni podaci za prijavu.',
            ])->withInput($request->only('email'));
        }

        if (!$korisnik->aktivan) {
            return back()->withErrors([
                'email' => 'Vaš nalog je deaktiviran.',
            ])->withInput($request->only('email'));
        }

        Auth::login($korisnik, $request->filled('remember'));

        $korisnik->poslednja_prijava = now();
        $korisnik->save();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
