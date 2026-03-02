<?php

namespace App\Http\Controllers;

use App\Models\Korisnik;
use App\Models\Farmaceut;
use App\Models\AdminApoteke;
use App\Models\CentralniAdmin;
use App\Models\RegistrovaniKorisnik;
use App\Models\Apoteka;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KorisnikController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Korisnik::with('apoteka');

        if ($user->isAdminApoteke()) {
            $query->where('apoteka_id', $user->apoteka_id);
        }

        if ($request->filled('tip')) {
            $query->where('tip', $request->input('tip'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ime', 'LIKE', "%{$search}%")
                  ->orWhere('prezime', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $korisnici = $query->orderBy('prezime')->orderBy('ime')->paginate(20);

        $apoteke = Apoteka::where('aktivna', true)->orderBy('naziv')->get();

        return view('korisnici.index', compact('korisnici', 'apoteke'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $apoteke = $user->isCentralniAdmin()
            ? Apoteka::where('aktivna', true)->orderBy('naziv')->get()
            : Apoteka::where('id', $user->apoteka_id)->get();

        $tipovi = $user->isCentralniAdmin()
            ? UserType::cases()
            : [UserType::FARMACEUT];

        return view('korisnici.create', compact('apoteke', 'tipovi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ime' => 'required|string|max:100',
            'prezime' => 'required|string|max:100',
            'email' => 'required|email|unique:korisnici,email',
            'password' => 'required|string|min:8|confirmed',
            'tip' => 'required|in:' . implode(',', UserType::values()),
            'apoteka_id' => 'nullable|exists:apoteke,id',
            'licenca' => 'nullable|required_if:tip,F|string|max:50',
            'jmbg' => 'nullable|required_if:tip,R|string|size:13',
        ]);

        DB::transaction(function () use ($validated) {
            $korisnik = Korisnik::create([
                'ime' => $validated['ime'],
                'prezime' => $validated['prezime'],
                'email' => $validated['email'],
                'lozinka_hash' => Hash::make($validated['password']),
                'tip' => $validated['tip'],
                'apoteka_id' => $validated['apoteka_id'] ?? null,
                'aktivan' => true,
            ]);

            // Create subtype record
            match(UserType::from($validated['tip'])) {
                UserType::FARMACEUT => Farmaceut::create([
                    'id' => $korisnik->id,
                    'licenca' => $validated['licenca'],
                ]),
                UserType::ADMIN_APOTEKE => AdminApoteke::create([
                    'id' => $korisnik->id,
                ]),
                UserType::CENTRALNI_ADMIN => CentralniAdmin::create([
                    'id' => $korisnik->id,
                ]),
                UserType::REGISTROVANI_KORISNIK => RegistrovaniKorisnik::create([
                    'id' => $korisnik->id,
                    'jmbg' => $validated['jmbg'],
                ]),
            };
        });

        return redirect()->route('korisnici.index')
                        ->with('success', 'Korisnik je uspešno kreiran.');
    }

    public function show(Korisnik $korisnik)
    {
        $korisnik->load(['apoteka', 'farmaceut', 'adminApoteke', 'centralniAdmin']);
        return view('korisnici.show', compact('korisnik'));
    }

    public function edit(Korisnik $korisnik)
    {
        $apoteke = Apoteka::where('aktivna', true)->orderBy('naziv')->get();
        $korisnik->load(['farmaceut', 'adminApoteke', 'centralniAdmin']);

        return view('korisnici.edit', compact('korisnik', 'apoteke'));
    }

    public function update(Request $request, Korisnik $korisnik)
    {
        $rules = [
            'ime' => 'required|string|max:100',
            'prezime' => 'required|string|max:100',
            'email' => 'required|email|unique:korisnici,email,' . $korisnik->id,
            'apoteka_id' => 'nullable|exists:apoteke,id',
            'aktivan' => 'boolean',
            'licenca' => 'nullable|string|max:50',
        ];

        // Dodaj validaciju lozinke samo ako je popunjena
        $password = $request->input('password');
        if ($password !== null && $password !== '') {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $korisnik->ime = $validated['ime'];
        $korisnik->prezime = $validated['prezime'];
        $korisnik->email = $validated['email'];
        $korisnik->apoteka_id = $validated['apoteka_id'] ?? $korisnik->apoteka_id;
        $korisnik->aktivan = $validated['aktivan'] ?? $korisnik->aktivan;

        if (!empty($validated['password'])) {
            $korisnik->lozinka_hash = Hash::make($validated['password']);
        }

        $korisnik->save();

        // Ažuriraj podatke farmaceuta ako postoje
        if ($korisnik->isFarmaceut() && $korisnik->farmaceut && isset($validated['licenca'])) {
            $korisnik->farmaceut->licenca = $validated['licenca'];
            $korisnik->farmaceut->save();
        }

        return redirect()->route('korisnici.show', $korisnik)
                        ->with('success', 'Korisnik je uspešno ažuriran.');
    }

    public function destroy(Korisnik $korisnik)
    {
        $korisnik->aktivan = false;
        $korisnik->save();

        return redirect()->route('korisnici.index')
                        ->with('success', 'Korisnik je deaktiviran.');
    }
}
