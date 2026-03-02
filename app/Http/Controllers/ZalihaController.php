<?php

namespace App\Http\Controllers;

use App\Models\Zaliha;
use App\Models\Lek;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class ZalihaController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Zaliha::with(['lek', 'apoteka']);

        if (!$user->isCentralniAdmin()) {
            $query->where('apoteka_id', $user->apoteka_id);
        } elseif ($request->filled('apoteka_id')) {
            $query->where('apoteka_id', $request->input('apoteka_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('lek', function ($q) use ($search) {
                $q->where('naziv', 'LIKE', "%{$search}%")
                  ->orWhere('jkl_sifra', 'LIKE', "%{$search}%");
            });
        }

        if ($request->input('niske_zalihe') === '1') {
            $query->whereRaw('kolicina <= min_zaliha');
        }

        $zalihe = $query->orderBy('lek_id')->paginate(20);

        $niskeZalihe = $this->inventoryService->getLowStockItems(
            $user->isCentralniAdmin() ? null : $user->apoteka_id
        );

        return view('zalihe.index', compact('zalihe', 'niskeZalihe'));
    }

    public function edit(Request $request, int $apotekaId, int $lekId)
    {
        $zaliha = Zaliha::where('apoteka_id', $apotekaId)
                        ->where('lek_id', $lekId)
                        ->with(['lek', 'apoteka'])
                        ->firstOrFail();

        return view('zalihe.edit', compact('zaliha'));
    }

    public function update(Request $request, int $apotekaId, int $lekId)
    {
        $validated = $request->validate([
            'kolicina' => 'required|integer|min:0',
            'prodajna_cena' => 'required|numeric|min:0',
            'min_zaliha' => 'required|integer|min:0',
        ]);

        $zaliha = Zaliha::where('apoteka_id', $apotekaId)
                        ->where('lek_id', $lekId)
                        ->firstOrFail();

        $zaliha->kolicina = $validated['kolicina'];
        $zaliha->prodajna_cena = $validated['prodajna_cena'];
        $zaliha->min_zaliha = $validated['min_zaliha'];
        $zaliha->datum_azuriranja = now();
        $zaliha->save();

        return redirect()->route('zalihe.index')
                        ->with('success', 'Zaliha je uspešno ažurirana.');
    }

    public function dodajLek(Request $request)
    {
        $validated = $request->validate([
            'lek_id' => 'required|exists:lekovi,id',
            'kolicina' => 'required|integer|min:1',
            'prodajna_cena' => 'required|numeric|min:0',
            'min_zaliha' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();

        $this->inventoryService->increaseStock(
            $user->apoteka_id,
            $validated['lek_id'],
            $validated['kolicina'],
            $validated['prodajna_cena'],
            $validated['min_zaliha'] ?? null
        );

        return redirect()->route('zalihe.index')
                        ->with('success', 'Lek je dodat na zalihe.');
    }
}
