<?php

namespace App\Http\Controllers;

use App\Models\Lek;
use Illuminate\Http\Request;

class LekController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Lek::query();

        if (!$user->isCentralniAdmin()) {
            $query->whereHas('zalihe', function ($q) use ($user) {
                $q->where('apoteka_id', $user->apoteka_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('naziv', 'LIKE', "%{$search}%")
                  ->orWhere('jkl_sifra', 'LIKE', "%{$search}%")
                  ->orWhere('proizvodjac', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('na_recept')) {
            $query->where('na_recept', $request->input('na_recept') === '1');
        }

        $lekovi = $query->orderBy('naziv')->paginate(20);

        return view('lekovi.index', compact('lekovi'));
    }

    public function create()
    {
        return view('lekovi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'proizvodjac' => 'nullable|string|max:255',
            'jkl_sifra' => 'required|string|max:50|unique:lekovi,jkl_sifra',
            'farm_oblik' => 'nullable|string|max:100',
            'jacina' => 'nullable|string|max:100',
            'pakovanje' => 'nullable|string|max:100',
            'na_recept' => 'boolean',
        ]);

        $lek = Lek::create($validated);

        return redirect()->route('lekovi.show', $lek)
                        ->with('success', 'Lek je uspešno dodat.');
    }

    public function show(Request $request, Lek $lek)
    {
        $user = $request->user();

        if ($user->isCentralniAdmin()) {
            $lek->load(['zalihe.apoteka', 'dobavljaci']);
        } else {
            $lek->load(['dobavljaci']);
            $lek->setRelation('zalihe',
                $lek->zalihe()->with('apoteka')
                    ->where('apoteka_id', $user->apoteka_id)
                    ->get()
            );
        }

        return view('lekovi.show', compact('lek'));
    }

    public function edit(Lek $lek)
    {
        return view('lekovi.edit', compact('lek'));
    }

    public function update(Request $request, Lek $lek)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'proizvodjac' => 'nullable|string|max:255',
            'jkl_sifra' => 'required|string|max:50|unique:lekovi,jkl_sifra,' . $lek->id,
            'farm_oblik' => 'nullable|string|max:100',
            'jacina' => 'nullable|string|max:100',
            'pakovanje' => 'nullable|string|max:100',
            'na_recept' => 'boolean',
        ]);

        $lek->update($validated);

        return redirect()->route('lekovi.show', $lek)
                        ->with('success', 'Lek je uspešno ažuriran.');
    }

    public function destroy(Lek $lek)
    {
        if ($lek->zalihe()->where('kolicina', '>', 0)->exists()) {
            return back()->with('error', 'Ne može se obrisati lek koji ima zalihe.');
        }

        $lek->delete();

        return redirect()->route('lekovi.index')
                        ->with('success', 'Lek je uspešno obrisan.');
    }
}
