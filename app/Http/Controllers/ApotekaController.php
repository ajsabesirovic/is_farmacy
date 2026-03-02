<?php

namespace App\Http\Controllers;

use App\Models\Apoteka;
use App\Enums\UserType;
use Illuminate\Http\Request;

class ApotekaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isCentralniAdmin()) {
            $apoteke = Apoteka::orderBy('naziv')->paginate(15);
        } else {
            $apoteke = Apoteka::where('id', $user->apoteka_id)->paginate(15);
        }

        return view('apoteke.index', compact('apoteke'));
    }

    public function create()
    {
        return view('apoteke.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'adresa' => 'required|string|max:255',
            'grad' => 'required|string|max:100',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['aktivna'] = true;

        $apoteka = Apoteka::create($validated);

        return redirect()->route('apoteke.show', $apoteka)
                        ->with('success', 'Apoteka je uspešno kreirana.');
    }

    public function show(Apoteka $apoteka)
    {
        $apoteka->load(['korisnici', 'zalihe.lek', 'prodaje', 'narudzbenice']);
        return view('apoteke.show', compact('apoteka'));
    }

    public function edit(Apoteka $apoteka)
    {
        return view('apoteke.edit', compact('apoteka'));
    }

    public function update(Request $request, Apoteka $apoteka)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'adresa' => 'required|string|max:255',
            'grad' => 'required|string|max:100',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'aktivna' => 'boolean',
        ]);

        $apoteka->update($validated);

        return redirect()->route('apoteke.show', $apoteka)
                        ->with('success', 'Apoteka je uspešno ažurirana.');
    }

    public function destroy(Apoteka $apoteka)
    {
        $apoteka->aktivna = false;
        $apoteka->save();

        return redirect()->route('apoteke.index')
                        ->with('success', 'Apoteka je deaktivirana.');
    }
}
