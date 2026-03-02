<?php

namespace App\Http\Controllers;

use App\Models\Dobavljac;
use App\Models\Lek;
use Illuminate\Http\Request;

class DobavljacController extends Controller
{
    public function index(Request $request)
    {
        $query = Dobavljac::withCount(['lekovi', 'narudzbenice']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('naziv', 'LIKE', "%{$search}%")
                  ->orWhere('pib', 'LIKE', "%{$search}%");
            });
        }

        if ($request->input('samo_aktivni') === '1') {
            $query->where('aktivan', true);
        }

        $dobavljaci = $query->orderBy('naziv')->paginate(20);

        return view('dobavljaci.index', compact('dobavljaci'));
    }

    public function create()
    {
        $lekovi = Lek::orderBy('naziv')->get();

        return view('dobavljaci.create', compact('lekovi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'pib' => 'required|string|max:20|unique:dobavljaci,pib',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $dobavljac = Dobavljac::create($validated);

        return redirect()->route('dobavljaci.show', $dobavljac)
                        ->with('success', 'Dobavljač je uspešno dodat.');
    }

    public function show(Dobavljac $dobavljac)
    {
        $dobavljac->load(['lekovi', 'narudzbenice' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('dobavljaci.show', compact('dobavljac'));
    }

    public function edit(Dobavljac $dobavljac)
    {
        $lekovi = Lek::orderBy('naziv')->get();

        return view('dobavljaci.edit', compact('dobavljac', 'lekovi'));
    }

    public function update(Request $request, Dobavljac $dobavljac)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'pib' => 'required|string|max:20|unique:dobavljaci,pib,' . $dobavljac->id,
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'aktivan' => 'boolean',
        ]);

        $dobavljac->update($validated);

        return redirect()->route('dobavljaci.show', $dobavljac)
                        ->with('success', 'Dobavljač je uspešno ažuriran.');
    }

    public function destroy(Dobavljac $dobavljac)
    {
        $dobavljac->aktivan = false;
        $dobavljac->save();

        return redirect()->route('dobavljaci.index')
                        ->with('success', 'Dobavljač je deaktiviran.');
    }

    public function addLek(Request $request, Dobavljac $dobavljac)
    {
        $validated = $request->validate([
            'lek_id' => 'required|exists:lekovi,id',
            'nabavna_cena' => 'required|numeric|min:0',
        ]);

        $dobavljac->lekovi()->attach($validated['lek_id'], [
            'nabavna_cena' => $validated['nabavna_cena'],
        ]);

        return back()->with('success', 'Lek je dodat dobavljaču.');
    }
}
