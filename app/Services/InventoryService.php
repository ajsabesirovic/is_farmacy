<?php

namespace App\Services;

use App\Models\Zaliha;
use App\Models\Lek;
use App\Models\Apoteka;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function decreaseStock(int $apotekaId, int $lekId, int $quantity): Zaliha
    {
        $zaliha = Zaliha::where('apoteka_id', $apotekaId)
                        ->where('lek_id', $lekId)
                        ->firstOrFail();

        if ($zaliha->kolicina < $quantity) {
            throw new \Exception("Nedovoljna količina na zalihi. Dostupno: {$zaliha->kolicina}, traženo: {$quantity}");
        }

        $zaliha->kolicina -= $quantity;
        $zaliha->datum_azuriranja = now();
        $zaliha->save();

        return $zaliha;
    }

    public function increaseStock(
        int $apotekaId,
        int $lekId,
        int $quantity,
        ?float $cena = null,
        ?int $minZaliha = null
    ): Zaliha {
        $zaliha = Zaliha::where('apoteka_id', $apotekaId)
                        ->where('lek_id', $lekId)
                        ->first();

        if ($zaliha) {
            $zaliha->kolicina += $quantity;
            if ($cena !== null) {
                $zaliha->prodajna_cena = $cena;
            }
            if ($minZaliha !== null) {
                $zaliha->min_zaliha = $minZaliha;
            }
            $zaliha->datum_azuriranja = now();
            $zaliha->save();
        } else {
            $zaliha = Zaliha::create([
                'apoteka_id' => $apotekaId,
                'lek_id' => $lekId,
                'kolicina' => $quantity,
                'prodajna_cena' => $cena ?? 0,
                'min_zaliha' => $minZaliha ?? 10,
                'datum_azuriranja' => now(),
            ]);
        }

        return $zaliha;
    }

    public function updatePrice(int $apotekaId, int $lekId, float $novaCena): Zaliha
    {
        $zaliha = Zaliha::where('apoteka_id', $apotekaId)
                        ->where('lek_id', $lekId)
                        ->firstOrFail();

        $zaliha->prodajna_cena = $novaCena;
        $zaliha->datum_azuriranja = now();
        $zaliha->save();

        return $zaliha;
    }

    public function getLowStockItems(?int $apotekaId = null): Collection
    {
        $query = Zaliha::whereRaw('kolicina <= min_zaliha')
                       ->with(['lek', 'apoteka']);

        if ($apotekaId) {
            $query->where('apoteka_id', $apotekaId);
        }

        return $query->get();
    }

    public function getOutOfStockItems(?int $apotekaId = null): Collection
    {
        $query = Zaliha::where('kolicina', '<=', 0)
                       ->with(['lek', 'apoteka']);

        if ($apotekaId) {
            $query->where('apoteka_id', $apotekaId);
        }

        return $query->get();
    }

    public function checkAvailability(int $lekId): Collection
    {
        return Zaliha::where('lek_id', $lekId)
                     ->where('kolicina', '>', 0)
                     ->with(['apoteka' => function ($query) {
                         $query->where('aktivna', true);
                     }])
                     ->get()
                     ->filter(fn($z) => $z->apoteka !== null);
    }

    public function getInventoryValue(?int $apotekaId = null): float
    {
        $query = Zaliha::query();

        if ($apotekaId) {
            $query->where('apoteka_id', $apotekaId);
        }

        return $query->get()->sum(function ($zaliha) {
            return $zaliha->kolicina * $zaliha->prodajna_cena;
        });
    }
}
