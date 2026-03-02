<?php

namespace App\Http\Controllers;

use App\Models\Prodaja;
use App\Models\Lek;
use App\Models\Recept;
use App\Models\Zaliha;
use App\Enums\PaymentMethod;
use App\Services\SaleService;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;

class ProdajaController extends Controller
{
    protected SaleService $saleService;
    protected PrescriptionService $prescriptionService;

    public function __construct(SaleService $saleService, PrescriptionService $prescriptionService)
    {
        $this->saleService = $saleService;
        $this->prescriptionService = $prescriptionService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $apotekaId = $user->apoteka_id;

        $query = Prodaja::with(['korisnik', 'recept', 'stavke.lek']);

        if (!$user->isCentralniAdmin()) {
            $query->where('apoteka_id', $apotekaId);
        }

        if ($request->filled('datum_od')) {
            $query->where('datum', '>=', $request->input('datum_od'));
        }

        if ($request->filled('datum_do')) {
            $query->where('datum', '<=', $request->input('datum_do'));
        }

        $prodaje = $query->orderByDesc('datum')
                         ->orderByDesc('vreme')
                         ->paginate(20);

        return view('prodaje.index', compact('prodaje'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $apotekaId = $user->apoteka_id;

        $zalihe = Zaliha::where('apoteka_id', $apotekaId)
                        ->where('kolicina', '>', 0)
                        ->with('lek')
                        ->get();

        $nacinPlacanja = PaymentMethod::cases();

        $recept = null;
        if ($request->has('recept')) {
            $recept = Recept::with('lekovi')->find($request->input('recept'));
        }

        return view('prodaje.create', compact('zalihe', 'nacinPlacanja', 'recept'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nacin_placanja' => 'required|in:' . implode(',', PaymentMethod::values()),
            'recept_id' => 'nullable|exists:recepti,id',
            'stavke' => 'required|array|min:1',
            'stavke.*.lek_id' => 'required|exists:lekovi,id',
            'stavke.*.kolicina' => 'required|integer|min:1',
            'stavke.*.popust' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();

        try {
            $prodaja = $this->saleService->createSale([
                'nacin_placanja' => $validated['nacin_placanja'],
                'apoteka_id' => $user->apoteka_id,
                'korisnik_id' => $user->id,
                'recept_id' => $validated['recept_id'] ?? null,
            ], $validated['stavke']);

            return redirect()->route('prodaje.show', $prodaja)
                            ->with('success', 'Prodaja je uspešno evidentirana.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', $e->getMessage());
        }
    }

    public function show(Prodaja $prodaja)
    {
        $prodaja->load(['apoteka', 'korisnik', 'recept.lekovi', 'recept.lekar', 'stavke.lek']);
        return view('prodaje.show', compact('prodaja'));
    }

    public function validateRecept(Request $request)
    {
        $request->validate([
            'broj_recepta' => 'required|string',
            'jmbg' => 'required|string|size:13',
        ], [
            'broj_recepta.required' => 'Broj recepta je obavezan.',
            'jmbg.required' => 'JMBG pacijenta je obavezan.',
            'jmbg.size' => 'JMBG mora imati tačno 13 cifara.',
        ]);

        try {
            $result = $this->prescriptionService->validateForSaleWithQuantities(
                $request->input('broj_recepta'),
                $request->input('jmbg')
            );

            return response()->json([
                'recept' => [
                    'id' => $result['recept']->id,
                    'broj_recepta' => $result['recept']->broj_recepta,
                    'pacijent' => $result['pacijent'],
                    'datum_vazenja' => $result['datum_vazenja'],
                ],
                'lekovi' => $result['lekovi'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
