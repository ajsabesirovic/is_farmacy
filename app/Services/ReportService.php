<?php

namespace App\Services;

use App\Models\Prodaja;
use App\Models\Zaliha;
use App\Models\StavkaProdaje;
use App\Models\Narudzbenica;
use App\Models\Recept;
use App\Enums\PrescriptionStatus;
use App\Enums\OrderStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getSalesReport(int $apotekaId, string $from, string $to): array
    {
        $sales = Prodaja::where('apoteka_id', $apotekaId)
                        ->whereBetween('datum', [$from, $to])
                        ->with('stavke.lek')
                        ->get();

        $byPaymentMethod = $sales->groupBy(fn($s) => $s->nacin_placanja->value)
                                 ->map(fn($group) => [
                                     'broj' => $group->count(),
                                     'iznos' => $group->sum('ukupan_iznos'),
                                 ]);

        $dailyBreakdown = $sales->groupBy('datum')
                                ->map(fn($group) => [
                                    'broj_prodaja' => $group->count(),
                                    'ukupan_iznos' => $group->sum('ukupan_iznos'),
                                ])
                                ->sortKeys();

        return [
            'period' => ['od' => $from, 'do' => $to],
            'ukupno_prodaja' => $sales->count(),
            'ukupan_promet' => $sales->sum('ukupan_iznos'),
            'prosecna_vrednost' => $sales->count() > 0 ? $sales->sum('ukupan_iznos') / $sales->count() : 0,
            'po_nacinu_placanja' => $byPaymentMethod,
            'dnevni_pregled' => $dailyBreakdown,
        ];
    }

    public function getMostRequestedMedicines(int $limit = 10, ?int $apotekaId = null, ?string $from = null, ?string $to = null): Collection
    {
        $query = StavkaProdaje::select('lek_id', DB::raw('SUM(kolicina) as ukupno_prodato'), DB::raw('COUNT(*) as broj_prodaja'))
                              ->groupBy('lek_id')
                              ->orderByDesc('ukupno_prodato')
                              ->limit($limit)
                              ->with('lek');

        if ($apotekaId) {
            $query->whereHas('prodaja', fn($q) => $q->where('apoteka_id', $apotekaId));
        }

        if ($from && $to) {
            $query->whereHas('prodaja', fn($q) => $q->whereBetween('datum', [$from, $to]));
        }

        return $query->get()->map(fn($item) => [
            'lek_id' => $item->lek_id,
            'naziv' => $item->lek->naziv,
            'proizvodjac' => $item->lek->proizvodjac,
            'jkl_sifra' => $item->lek->jkl_sifra,
            'ukupno_prodato' => $item->ukupno_prodato,
            'broj_prodaja' => $item->broj_prodaja,
        ]);
    }

    public function getInventoryReport(?int $apotekaId = null): Collection
    {
        $query = Zaliha::with(['lek', 'apoteka']);

        if ($apotekaId) {
            $query->where('apoteka_id', $apotekaId);
        }

        return $query->get()->map(fn($z) => [
            'apoteka' => $z->apoteka->naziv,
            'apoteka_id' => $z->apoteka_id,
            'lek' => $z->lek->naziv,
            'lek_id' => $z->lek_id,
            'jkl_sifra' => $z->lek->jkl_sifra,
            'kolicina' => $z->kolicina,
            'min_zaliha' => $z->min_zaliha,
            'cena' => $z->prodajna_cena,
            'vrednost' => $z->kolicina * $z->prodajna_cena,
            'status' => $z->isLowStock() ? 'NISKA_ZALIHA' : ($z->isOutOfStock() ? 'NEMA' : 'OK'),
        ]);
    }

    public function getInventorySummary(?int $apotekaId = null): array
    {
        $query = Zaliha::query();

        if ($apotekaId) {
            $query->where('apoteka_id', $apotekaId);
        }

        $zalihe = $query->get();

        return [
            'ukupna_vrednost' => $zalihe->sum(fn($z) => $z->kolicina * $z->prodajna_cena),
            'broj_artikala' => $zalihe->count(),
            'niske_zalihe' => $zalihe->filter(fn($z) => $z->isLowStock())->count(),
            'bez_zaliha' => $zalihe->filter(fn($z) => $z->isOutOfStock())->count(),
        ];
    }

    public function getPrescriptionReport(?string $from = null, ?string $to = null): array
    {
        $query = Recept::query();

        if ($from && $to) {
            $query->whereBetween('datum_izdavanja', [$from, $to]);
        }

        $recepti = $query->get();

        $byStatus = $recepti->groupBy(fn($r) => $r->status->value)
                           ->map(fn($group) => $group->count());

        return [
            'ukupno' => $recepti->count(),
            'po_statusu' => $byStatus,
            'realizovano' => $recepti->where('status', PrescriptionStatus::REALIZOVAN)->count(),
            'ceka_realizaciju' => $recepti->where('status', PrescriptionStatus::IZDAT)->count(),
            'isteklo' => $recepti->where('status', PrescriptionStatus::ISTEKAO)->count(),
        ];
    }

    public function getSupplierReport(?int $dobavljacId = null, ?string $from = null, ?string $to = null): Collection
    {
        $query = Narudzbenica::with(['dobavljac', 'apoteka', 'stavke']);

        if ($dobavljacId) {
            $query->where('dobavljac_id', $dobavljacId);
        }

        if ($from && $to) {
            $query->whereBetween('datum_kreiranja', [$from, $to]);
        }

        $narudzbenice = $query->get();

        return $narudzbenice->groupBy('dobavljac_id')->map(function ($group) {
            $dobavljac = $group->first()->dobavljac;
            return [
                'dobavljac_id' => $dobavljac->id,
                'naziv' => $dobavljac->naziv,
                'ukupno_narudzbi' => $group->count(),
                'ukupna_vrednost' => $group->sum('ukupna_vrednost'),
                'isporuceno' => $group->where('status', OrderStatus::ISPORUCENA)->count(),
                'u_toku' => $group->whereIn('status', [OrderStatus::NACRT, OrderStatus::POSLATA])->count(),
                'otkazano' => $group->where('status', OrderStatus::OTKAZANA)->count(),
            ];
        })->values();
    }
}
