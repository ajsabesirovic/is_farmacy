<?php

namespace App\Services;

use App\Models\Lek;
use App\Models\Zaliha;
use Illuminate\Support\Collection;

class MedicineSearchService
{
    public function search(string $query, ?string $grad = null): Collection
    {
        $lekovi = Lek::where(function ($q) use ($query) {
                        $q->where('naziv', 'LIKE', "%{$query}%")
                          ->orWhere('jkl_sifra', 'LIKE', "%{$query}%")
                          ->orWhere('proizvodjac', 'LIKE', "%{$query}%");
                    })
                    ->with(['zalihe' => function ($q) {
                        $q->where('kolicina', '>', 0)
                          ->with(['apoteka' => function ($q) {
                              $q->where('aktivna', true);
                          }]);
                    }])
                    ->get();

        return $lekovi->map(function ($lek) use ($grad) {
            $dostupnost = $lek->zalihe
                ->filter(fn($z) => $z->apoteka !== null)
                ->when($grad, fn($collection) => $collection->filter(
                    fn($z) => strtolower($z->apoteka->grad) === strtolower($grad)
                ))
                ->map(fn($z) => [
                    'apoteka_id' => $z->apoteka->id,
                    'apoteka_naziv' => $z->apoteka->naziv,
                    'adresa' => $z->apoteka->adresa,
                    'grad' => $z->apoteka->grad,
                    'telefon' => $z->apoteka->telefon,
                    'kolicina' => $z->kolicina,
                    'prodajna_cena' => $z->prodajna_cena,
                ])
                ->values();

            return [
                'id' => $lek->id,
                'naziv' => $lek->naziv,
                'proizvodjac' => $lek->proizvodjac,
                'jkl_sifra' => $lek->jkl_sifra,
                'farm_oblik' => $lek->farm_oblik,
                'jacina' => $lek->jacina,
                'pakovanje' => $lek->pakovanje,
                'na_recept' => $lek->na_recept,
                'dostupnost' => $dostupnost,
                'ukupno_dostupno' => $dostupnost->sum('kolicina'),
                'min_cena' => $dostupnost->min('prodajna_cena'),
                'max_cena' => $dostupnost->max('prodajna_cena'),
            ];
        })->filter(fn($lek) => $lek['dostupnost']->isNotEmpty());
    }

    public function getMedicineAvailability(int $lekId): array
    {
        $lek = Lek::with(['zalihe' => function ($q) {
                        $q->where('kolicina', '>', 0)
                          ->with(['apoteka' => function ($q) {
                              $q->where('aktivna', true);
                          }]);
                    }])
                    ->findOrFail($lekId);

        $dostupnost = $lek->zalihe
            ->filter(fn($z) => $z->apoteka !== null)
            ->map(fn($z) => [
                'apoteka_id' => $z->apoteka->id,
                'apoteka_naziv' => $z->apoteka->naziv,
                'adresa' => $z->apoteka->adresa,
                'grad' => $z->apoteka->grad,
                'telefon' => $z->apoteka->telefon,
                'kolicina' => $z->kolicina,
                'prodajna_cena' => $z->prodajna_cena,
            ])
            ->sortBy('prodajna_cena')
            ->values();

        return [
            'lek' => [
                'id' => $lek->id,
                'naziv' => $lek->naziv,
                'proizvodjac' => $lek->proizvodjac,
                'jkl_sifra' => $lek->jkl_sifra,
                'farm_oblik' => $lek->farm_oblik,
                'jacina' => $lek->jacina,
                'na_recept' => $lek->na_recept,
            ],
            'dostupnost' => $dostupnost,
            'ukupno_dostupno' => $dostupnost->sum('kolicina'),
            'broj_apoteka' => $dostupnost->count(),
        ];
    }

    public function getAvailableCities(): Collection
    {
        return Zaliha::where('kolicina', '>', 0)
            ->join('apoteke', 'zalihe.apoteka_id', '=', 'apoteke.id')
            ->where('apoteke.aktivna', true)
            ->select('apoteke.grad')
            ->distinct()
            ->orderBy('apoteke.grad')
            ->pluck('grad');
    }
}
