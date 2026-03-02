<?php

namespace App\Services;

use App\Models\Recept;
use App\Enums\PrescriptionStatus;
use Illuminate\Support\Collection;

class PrescriptionService
{
    public function validateForSale(int $receptId): Recept
    {
        $recept = Recept::with('lekovi')->findOrFail($receptId);

        if ($recept->status === PrescriptionStatus::REALIZOVAN) {
            throw new \Exception("Recept je već realizovan.");
        }

        if ($recept->status === PrescriptionStatus::ISTEKAO) {
            throw new \Exception("Recept je istekao.");
        }

        if ($recept->datum_vazenja < now()->toDateString()) {
            $recept->status = PrescriptionStatus::ISTEKAO;
            $recept->save();
            throw new \Exception("Recept je istekao.");
        }

        return $recept;
    }

    public function realize(int $receptId): Recept
    {
        $recept = Recept::findOrFail($receptId);
        $recept->status = PrescriptionStatus::REALIZOVAN;
        $recept->save();

        return $recept;
    }

    public function checkAndUpdateExpired(): int
    {
        return Recept::where('status', PrescriptionStatus::IZDAT)
                     ->where('datum_vazenja', '<', now()->toDateString())
                     ->update(['status' => PrescriptionStatus::ISTEKAO]);
    }

    public function findByNumber(string $brojRecepta): ?Recept
    {
        return Recept::with('lekovi')
                     ->where('broj_recepta', $brojRecepta)
                     ->first();
    }

    /**
     * Traži recept po broju i JMBG-u pacijenta (sigurnija pretraga)
     */
    public function findByNumberAndJmbg(string $brojRecepta, string $jmbg): ?Recept
    {
        return Recept::with('lekovi')
                     ->where('broj_recepta', $brojRecepta)
                     ->where('jmbg_pacijenta', $jmbg)
                     ->first();
    }

    /**
     * Validira recept i vraća informacije o preostalim količinama
     */
    public function validateForSaleWithQuantities(string $brojRecepta, string $jmbg): array
    {
        $recept = $this->findByNumberAndJmbg($brojRecepta, $jmbg);

        if (!$recept) {
            throw new \Exception("Recept nije pronađen. Proverite broj recepta i JMBG pacijenta.");
        }

        if ($recept->status === PrescriptionStatus::REALIZOVAN) {
            throw new \Exception("Recept je već u potpunosti realizovan.");
        }

        if ($recept->status === PrescriptionStatus::ISTEKAO) {
            throw new \Exception("Recept je istekao.");
        }

        if ($recept->datum_vazenja < now()->toDateString()) {
            $recept->status = PrescriptionStatus::ISTEKAO;
            $recept->save();
            throw new \Exception("Recept je istekao.");
        }

        // Pripremi informacije o lekovima sa preostalim količinama
        $lekoviInfo = [];
        foreach ($recept->lekovi as $lek) {
            $propisano = $lek->pivot->kolicina;
            $izdato = $lek->pivot->izdata_kolicina;
            $preostalo = $propisano - $izdato;

            if ($preostalo > 0) {
                $lekoviInfo[] = [
                    'lek_id' => $lek->id,
                    'naziv' => $lek->naziv,
                    'propisano' => $propisano,
                    'izdato' => $izdato,
                    'preostalo' => $preostalo,
                    'doziranje' => $lek->pivot->doziranje,
                ];
            }
        }

        if (empty($lekoviInfo)) {
            throw new \Exception("Svi lekovi sa ovog recepta su već izdati.");
        }

        return [
            'recept' => $recept,
            'lekovi' => $lekoviInfo,
            'pacijent' => $recept->ime_pacijenta,
            'datum_vazenja' => $recept->datum_vazenja->format('d.m.Y'),
        ];
    }

    /**
     * Ažurira izdatu količinu leka na receptu
     */
    public function updateDispensedQuantity(int $receptId, int $lekId, int $izdataKolicina): void
    {
        $recept = Recept::with('lekovi')->findOrFail($receptId);
        $lek = $recept->lekovi()->where('lek_id', $lekId)->first();

        if (!$lek) {
            throw new \Exception("Lek nije pronađen na receptu.");
        }

        $propisano = $lek->pivot->kolicina;
        $vecIzdato = $lek->pivot->izdata_kolicina;
        $novoUkupno = $vecIzdato + $izdataKolicina;

        if ($novoUkupno > $propisano) {
            throw new \Exception(
                "Ne možete izdati više od propisane količine. " .
                "Propisano: {$propisano}, Već izdato: {$vecIzdato}, Pokušavate izdati: {$izdataKolicina}"
            );
        }

        // Ažuriraj izdatu količinu
        $recept->lekovi()->updateExistingPivot($lekId, [
            'izdata_kolicina' => $novoUkupno,
        ]);

        // Ako su svi lekovi izdati, označi recept kao realizovan
        $recept->refresh();
        if ($recept->isFullyDispensed()) {
            $recept->status = PrescriptionStatus::REALIZOVAN;
            $recept->save();
        }
    }

    public function getValidPrescriptions(): Collection
    {
        return Recept::where('status', PrescriptionStatus::IZDAT)
                     ->where('datum_vazenja', '>=', now()->toDateString())
                     ->with('lekovi')
                     ->orderBy('datum_vazenja')
                     ->get();
    }

    public function create(array $data, array $lekovi = []): Recept
    {
        $recept = Recept::create($data);

        foreach ($lekovi as $lek) {
            $recept->lekovi()->attach($lek['lek_id'], [
                'kolicina' => $lek['kolicina'],
                'doziranje' => $lek['doziranje'] ?? null,
            ]);
        }

        return $recept->load('lekovi');
    }
}
