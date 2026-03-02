<?php

namespace App\Http\Controllers;

use App\Models\Narudzbenica;
use App\Models\Dobavljac;
use App\Models\DobavljacLek;
use App\Enums\OrderStatus;
use App\Services\OrderService;
use Illuminate\Http\Request;

class NarudzbenicaController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Narudzbenica::with(['apoteka', 'dobavljac', 'korisnik']);

        if (!$user->isCentralniAdmin()) {
            $query->where('apoteka_id', $user->apoteka_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $narudzbenice = $query->orderByDesc('datum_kreiranja')->paginate(20);

        return view('narudzbenice.index', compact('narudzbenice'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $apoteke = collect();

        $dobavljaci = Dobavljac::where('aktivan', true)
                               ->orderBy('naziv')
                               ->get();

        if ($user->isCentralniAdmin()) {
            $apoteke = \App\Models\Apoteka::where('aktivna', true)
                                          ->orderBy('naziv')
                                          ->get();
        }

        $preselectedLek = null;
        $preselectedDobavljacId = null;
        if ($request->filled('lek_id')) {
            $preselectedLek = \App\Models\Lek::find($request->input('lek_id'));
            // Find first active supplier that carries this lek
            $dobavljacLek = DobavljacLek::where('lek_id', $request->input('lek_id'))
                                        ->first();
            if ($dobavljacLek) {
                $preselectedDobavljacId = $dobavljacLek->dobavljac_id;
            }
        }

        return view('narudzbenice.create', compact('dobavljaci', 'apoteke', 'preselectedLek', 'preselectedDobavljacId'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'dobavljac_id' => 'required|exists:dobavljaci,id',
            'napomena' => 'nullable|string',
            'stavke' => 'required|array|min:1',
            'stavke.*.lek_id' => 'required|exists:lekovi,id',
            'stavke.*.kolicina' => 'required|integer|min:1',
            'stavke.*.cena_po_komadu' => 'required|numeric|min:0',
        ];

        // Centralni admin mora da izabere apoteku
        if ($user->isCentralniAdmin()) {
            $rules['apoteka_id'] = 'required|exists:apoteke,id';
        }

        $validated = $request->validate($rules);

        // Odredi apoteka_id
        $apotekaId = $user->isCentralniAdmin()
            ? $validated['apoteka_id']
            : $user->apoteka_id;

        try {
            $narudzbenica = $this->orderService->createOrder([
                'dobavljac_id' => $validated['dobavljac_id'],
                'napomena' => $validated['napomena'],
                'apoteka_id' => $apotekaId,
                'korisnik_id' => $user->id,
            ], $validated['stavke']);

            return redirect()->route('narudzbenice.show', $narudzbenica)
                            ->with('success', 'Narudžbenica je uspešno kreirana.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', $e->getMessage());
        }
    }

    public function show(Narudzbenica $narudzbenica)
    {
        $narudzbenica->load(['apoteka', 'dobavljac', 'korisnik', 'stavke.lek']);
        return view('narudzbenice.show', compact('narudzbenica'));
    }

    public function updateStatus(Request $request, Narudzbenica $narudzbenica)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', OrderStatus::values()),
        ]);

        try {
            $this->orderService->updateStatus($narudzbenica->id, OrderStatus::from($validated['status']));
            return redirect()->route('narudzbenice.show', $narudzbenica)
                            ->with('success', 'Status narudžbenice je ažuriran.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markDelivered(Narudzbenica $narudzbenica)
    {
        try {
            $this->orderService->markAsDelivered($narudzbenica->id);
            return redirect()->route('narudzbenice.show', $narudzbenica)
                            ->with('success', 'Narudžbenica je označena kao isporučena. Zalihe su ažurirane.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Narudzbenica $narudzbenica)
    {
        try {
            $this->orderService->cancelOrder($narudzbenica->id);
            return redirect()->route('narudzbenice.show', $narudzbenica)
                            ->with('success', 'Narudžbenica je otkazana.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getLekovi(Request $request, int $dobavljacId)
    {
        $lekovi = DobavljacLek::where('dobavljac_id', $dobavljacId)
                              ->with('lek')
                              ->get();

        return response()->json($lekovi);
    }
}
