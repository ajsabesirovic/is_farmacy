<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Korisnik;
use App\Models\RegistrovaniKorisnik;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|email|unique:korisnici,email',
            'password' => 'required|string|min:8|confirmed',
            'jmbg' => 'required|string|size:13|unique:registrovani_korisnici,jmbg',
        ], [
            'ime.required' => 'Ime je obavezno.',
            'prezime.required' => 'Prezime je obavezno.',
            'email.required' => 'Email je obavezan.',
            'email.email' => 'Unesite validnu email adresu.',
            'email.unique' => 'Korisnik sa ovim emailom vec postoji.',
            'password.required' => 'Lozinka je obavezna.',
            'password.min' => 'Lozinka mora imati najmanje 8 karaktera.',
            'password.confirmed' => 'Lozinke se ne poklapaju.',
            'jmbg.required' => 'JMBG je obavezan.',
            'jmbg.size' => 'JMBG mora imati tacno 13 cifara.',
            'jmbg.unique' => 'Korisnik sa ovim JMBG vec postoji.',
        ]);

        DB::transaction(function () use ($request) {
            $korisnik = Korisnik::create([
                'ime' => $request->ime,
                'prezime' => $request->prezime,
                'email' => $request->email,
                'lozinka_hash' => Hash::make($request->password),
                'aktivan' => true,
                'tip' => UserType::REGISTROVANI_KORISNIK,
            ]);

            RegistrovaniKorisnik::create([
                'id' => $korisnik->id,
                'jmbg' => $request->jmbg,
            ]);

            Auth::login($korisnik);
            $korisnik->poslednja_prijava = now();
            $korisnik->save();
        });

        return redirect()->route('dashboard');
    }
}
