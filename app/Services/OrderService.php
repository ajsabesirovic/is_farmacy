<?php

namespace App\Services;

use App\Models\Narudzbenica;
use App\Models\StavkaNarudzbenice;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function createOrder(array $data, array $items): Narudzbenica
    {
        return DB::transaction(function () use ($data, $items) {
            $narudzbenica = Narudzbenica::create([
                'broj_narudzbenice' => $this->generateOrderNumber(),
                'datum_kreiranja' => now(),
                'status' => OrderStatus::NACRT,
                'napomena' => $data['napomena'] ?? null,
                'apoteka_id' => $data['apoteka_id'],
                'dobavljac_id' => $data['dobavljac_id'],
                'korisnik_id' => $data['korisnik_id'],
                'ukupna_vrednost' => 0,
            ]);

            $ukupnaVrednost = 0;

            foreach ($items as $index => $item) {
                StavkaNarudzbenice::create([
                    'narudzbenica_id' => $narudzbenica->id,
                    'redni_broj' => $index + 1,
                    'lek_id' => $item['lek_id'],
                    'kolicina' => $item['kolicina'],
                    'cena_po_komadu' => $item['cena_po_komadu'],
                ]);

                $ukupnaVrednost += $item['kolicina'] * $item['cena_po_komadu'];
            }

            $narudzbenica->ukupna_vrednost = $ukupnaVrednost;
            $narudzbenica->save();

            return $narudzbenica->load('stavke.lek', 'apoteka', 'dobavljac', 'korisnik');
        });
    }

    public function updateStatus(int $narudzbenicaId, OrderStatus $status): Narudzbenica
    {
        $narudzbenica = Narudzbenica::findOrFail($narudzbenicaId);

        // Validacija prelaza statusa
        $this->validateStatusTransition($narudzbenica->status, $status);

        $narudzbenica->status = $status;

        if ($status === OrderStatus::ISPORUCENA) {
            $narudzbenica->datum_isporuke = now()->toDateString();
        }

        $narudzbenica->save();

        return $narudzbenica;
    }

    public function markAsDelivered(int $narudzbenicaId): Narudzbenica
    {
        return DB::transaction(function () use ($narudzbenicaId) {
            $narudzbenica = Narudzbenica::with('stavke')->findOrFail($narudzbenicaId);

            if ($narudzbenica->status !== OrderStatus::POSLATA) {
                throw new \Exception("Narudžbenica mora biti poslata pre isporuke. Trenutni status: " . $narudzbenica->status->label());
            }

            // Ažuriranje zaliha
            foreach ($narudzbenica->stavke as $stavka) {
                $this->inventoryService->increaseStock(
                    $narudzbenica->apoteka_id,
                    $stavka->lek_id,
                    $stavka->kolicina,
                    $stavka->cena_po_komadu
                );
            }

            $narudzbenica->status = OrderStatus::ISPORUCENA;
            $narudzbenica->datum_isporuke = now()->toDateString();
            $narudzbenica->save();

            return $narudzbenica->load('stavke.lek', 'apoteka', 'dobavljac');
        });
    }

    public function cancelOrder(int $narudzbenicaId): Narudzbenica
    {
        $narudzbenica = Narudzbenica::findOrFail($narudzbenicaId);

        if (in_array($narudzbenica->status, [OrderStatus::ISPORUCENA, OrderStatus::OTKAZANA], true)) {
            throw new \Exception("Nije moguće otkazati narudžbenicu sa statusom: " . $narudzbenica->status->label());
        }

        $narudzbenica->status = OrderStatus::OTKAZANA;
        $narudzbenica->save();

        return $narudzbenica;
    }

    protected function validateStatusTransition(OrderStatus $current, OrderStatus $new): void
    {
        $allowed = match($current) {
            OrderStatus::NACRT => [OrderStatus::POSLATA, OrderStatus::OTKAZANA],
            OrderStatus::POSLATA => [OrderStatus::ISPORUCENA, OrderStatus::OTKAZANA],
            OrderStatus::ISPORUCENA => [],
            OrderStatus::OTKAZANA => [],
        };

        if (!in_array($new, $allowed, true)) {
            throw new \Exception("Nije dozvoljen prelaz iz statusa '{$current->label()}' u '{$new->label()}'");
        }
    }

    protected function generateOrderNumber(): string
    {
        return 'NAR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
