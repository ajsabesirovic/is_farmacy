<?php

namespace App\Services;

use App\Models\Prodaja;
use App\Models\StavkaProdaje;
use App\Models\Zaliha;
use App\Models\Lek;
use App\Enums\PrescriptionStatus;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected InventoryService $inventoryService;
    protected PrescriptionService $prescriptionService;

    public function __construct(
        InventoryService $inventoryService,
        PrescriptionService $prescriptionService
    ) {
        $this->inventoryService = $inventoryService;
        $this->prescriptionService = $prescriptionService;
    }

    public function createSale(array $data, array $items): Prodaja
    {
        return DB::transaction(function () use ($data, $items) {
            // Provera da li ima lekova na recept
            $lekIds = array_column($items, 'lek_id');
            $lekoviNaRecept = Lek::whereIn('id', $lekIds)
                                 ->where('na_recept', true)
                                 ->get();

            if ($lekoviNaRecept->isNotEmpty() && empty($data['recept_id'])) {
                $nazivi = $lekoviNaRecept->pluck('naziv')->join(', ');
                throw new \Exception("Sledeći lekovi se izdaju na recept i zahtevaju validan recept: {$nazivi}");
            }

            // Validacija recepta i provera količina ako postoji recept
            $recept = null;
            if (!empty($data['recept_id'])) {
                $recept = $this->prescriptionService->validateForSale($data['recept_id']);

                // Proveri da li se ne izdaje više nego što je propisano
                foreach ($items as $item) {
                    $lek = Lek::find($item['lek_id']);
                    if ($lek && $lek->na_recept) {
                        $preostalo = $recept->getPreostalaKolicina($item['lek_id']);
                        if ($preostalo === 0) {
                            throw new \Exception(
                                "Lek '{$lek->naziv}' nije na ovom receptu ili je već u potpunosti izdat."
                            );
                        }
                        if ($item['kolicina'] > $preostalo) {
                            throw new \Exception(
                                "Ne možete izdati {$item['kolicina']} kom leka '{$lek->naziv}'. " .
                                "Preostalo za izdavanje: {$preostalo} kom."
                            );
                        }
                    }
                }
            }

            // Provera dostupnosti zaliha
            foreach ($items as $item) {
                $zaliha = Zaliha::where('apoteka_id', $data['apoteka_id'])
                                ->where('lek_id', $item['lek_id'])
                                ->first();

                if (!$zaliha || $zaliha->kolicina < $item['kolicina']) {
                    $lek = Lek::find($item['lek_id']);
                    $dostupno = $zaliha ? $zaliha->kolicina : 0;
                    throw new \Exception(
                        "Nedovoljna količina leka '{$lek->naziv}' na zalihama. Dostupno: {$dostupno} kom."
                    );
                }
            }

            // Kreiranje prodaje
            $prodaja = Prodaja::create([
                'datum' => now()->toDateString(),
                'vreme' => now()->toTimeString(),
                'nacin_placanja' => $data['nacin_placanja'],
                'apoteka_id' => $data['apoteka_id'],
                'korisnik_id' => $data['korisnik_id'],
                'recept_id' => $data['recept_id'] ?? null,
                'ukupan_iznos' => 0,
            ]);

            $ukupanIznos = 0;

            // Kreiranje stavki i ažuriranje zaliha
            foreach ($items as $index => $item) {
                $zaliha = Zaliha::where('apoteka_id', $data['apoteka_id'])
                                ->where('lek_id', $item['lek_id'])
                                ->first();

                $cena = $item['cena_po_komadu'] ?? $zaliha->prodajna_cena;
                $popust = $item['popust'] ?? 0;

                StavkaProdaje::create([
                    'prodaja_id' => $prodaja->id,
                    'redni_broj' => $index + 1,
                    'lek_id' => $item['lek_id'],
                    'kolicina' => $item['kolicina'],
                    'cena_po_komadu' => $cena,
                    'popust' => $popust,
                ]);

                // Smanjenje zaliha
                $this->inventoryService->decreaseStock(
                    $data['apoteka_id'],
                    $item['lek_id'],
                    $item['kolicina']
                );

                $ukupanIznos += ($item['kolicina'] * $cena) - $popust;

                // Ažuriraj izdatu količinu na receptu (ako je lek na recept)
                if ($recept) {
                    $lek = Lek::find($item['lek_id']);
                    if ($lek && $lek->na_recept) {
                        $this->prescriptionService->updateDispensedQuantity(
                            $recept->id,
                            $item['lek_id'],
                            $item['kolicina']
                        );
                    }
                }
            }

            // Ažuriranje ukupnog iznosa
            $prodaja->ukupan_iznos = $ukupanIznos;
            $prodaja->save();

            return $prodaja->load('stavke.lek', 'apoteka', 'korisnik', 'recept');
        });
    }

    public function getSaleDetails(int $prodajaId): Prodaja
    {
        return Prodaja::with(['stavke.lek', 'apoteka', 'korisnik', 'recept'])
                      ->findOrFail($prodajaId);
    }

    public function getSalesForPharmacy(int $apotekaId, ?string $from = null, ?string $to = null)
    {
        $query = Prodaja::where('apoteka_id', $apotekaId)
                        ->with(['stavke.lek', 'korisnik']);

        if ($from && $to) {
            $query->whereBetween('datum', [$from, $to]);
        }

        return $query->orderByDesc('datum')->orderByDesc('vreme')->get();
    }
}
