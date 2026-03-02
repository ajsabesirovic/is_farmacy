<?php

namespace App\Http\Controllers;

use App\Models\Recept;
use App\Models\Lek;
use App\Models\Lekar;
use App\Enums\PrescriptionStatus;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;

class ReceptController extends Controller
{
    protected PrescriptionService $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    public function index(Request $request)
    {
        // Auto-expire prescriptions that have passed their validity date
        $this->prescriptionService->checkAndUpdateExpired();

        $query = Recept::with(['lekovi', 'lekar']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('broj_recepta', 'LIKE', "%{$search}%")
                  ->orWhere('ime_pacijenta', 'LIKE', "%{$search}%")
                  ->orWhere('jmbg_pacijenta', 'LIKE', "%{$search}%");
            });
        }

        $recepti = $query->orderByDesc('datum_izdavanja')->paginate(20);

        return view('recepti.index', compact('recepti'));
    }

    public function create()
    {
        $lekovi = Lek::where('na_recept', true)->orderBy('naziv')->get();
        $lekari = Lekar::orderBy('prezime')->get();
        return view('recepti.create', compact('lekovi', 'lekari'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'broj_recepta' => 'required|string|unique:recepti,broj_recepta',
            'datum_izdavanja' => 'required|date',
            'datum_vazenja' => 'required|date|after:datum_izdavanja',
            'dijagnoza_sifra' => 'nullable|string|max:20',
            'ime_pacijenta' => 'nullable|string|max:255',
            'jmbg_pacijenta' => 'required|string|size:13',
            'lekar_id' => 'required|exists:lekari,id',
            'napomena' => 'nullable|string',
            'lekovi' => 'required|array|min:1',
            'lekovi.*.lek_id' => 'required|exists:lekovi,id',
            'lekovi.*.kolicina' => 'required|integer|min:1',
            'lekovi.*.doziranje' => 'nullable|string|max:255',
        ]);

        $recept = $this->prescriptionService->create([
            'broj_recepta' => $validated['broj_recepta'],
            'datum_izdavanja' => $validated['datum_izdavanja'],
            'datum_vazenja' => $validated['datum_vazenja'],
            'dijagnoza_sifra' => $validated['dijagnoza_sifra'],
            'ime_pacijenta' => $validated['ime_pacijenta'],
            'jmbg_pacijenta' => $validated['jmbg_pacijenta'],
            'lekar_id' => $validated['lekar_id'],
            'napomena' => $validated['napomena'],
            'status' => PrescriptionStatus::IZDAT,
        ], $validated['lekovi']);

        return redirect()->route('recepti.show', $recept)
                        ->with('success', 'Recept je uspešno registrovan.');
    }

    public function show(Recept $recept)
    {
        $recept->load(['lekovi', 'prodaja', 'lekar']);
        return view('recepti.show', compact('recept'));
    }

    public function validacija(Request $request)
    {
        $request->validate([
            'broj_recepta' => 'required|string',
        ]);

        $recept = $this->prescriptionService->findByNumber($request->input('broj_recepta'));

        if (!$recept) {
            return back()->with('error', 'Recept nije pronađen.');
        }

        return view('recepti.validate', compact('recept'));
    }

}
