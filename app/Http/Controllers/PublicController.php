<?php

namespace App\Http\Controllers;

use App\Services\MedicineSearchService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected MedicineSearchService $searchService;

    public function __construct(MedicineSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function landing()
    {
        return view('public.landing');
    }

    public function index()
    {
        $gradovi = $this->searchService->getAvailableCities();
        return view('public.pretraga', compact('gradovi'));
    }

    public function pretraga(Request $request)
    {
        $gradovi = $this->searchService->getAvailableCities();

        $rezultati = null;
        if ($request->filled('q')) {
            $rezultati = $this->searchService->search(
                $request->input('q'),
                $request->input('grad')
            );
        }

        return view('public.pretraga', [
            'gradovi' => $gradovi,
            'rezultati' => $rezultati,
            'query' => $request->input('q'),
            'izabraniGrad' => $request->input('grad'),
        ]);
    }

    public function lekDetalji(int $lekId)
    {
        $podaci = $this->searchService->getMedicineAvailability($lekId);
        return view('public.lek-detalji', $podaci);
    }
}
