<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Apoteka;
use App\Models\Dobavljac;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function prodaja(Request $request)
    {
        $user = $request->user();

        $apotekaId = $user->isCentralniAdmin()
            ? $request->input('apoteka_id')
            : $user->apoteka_id;

        $od = $request->input('od', now()->startOfMonth()->toDateString());
        $do = $request->input('do', now()->toDateString());

        $report = null;
        if ($apotekaId) {
            $report = $this->reportService->getSalesReport($apotekaId, $od, $do);
        }

        $apoteke = $user->isCentralniAdmin()
            ? Apoteka::where('aktivna', true)->orderBy('naziv')->get()
            : collect();

        return view('reports.prodaja', compact('report', 'apoteke', 'apotekaId', 'od', 'do'));
    }

    public function zalihe(Request $request)
    {
        $user = $request->user();

        $apotekaId = $user->isCentralniAdmin()
            ? $request->input('apoteka_id')
            : $user->apoteka_id;

        $report = $this->reportService->getInventoryReport($apotekaId);
        $summary = $this->reportService->getInventorySummary($apotekaId);

        $apoteke = $user->isCentralniAdmin()
            ? Apoteka::where('aktivna', true)->orderBy('naziv')->get()
            : collect();

        return view('reports.zalihe', compact('report', 'summary', 'apoteke', 'apotekaId'));
    }

    public function lekovi(Request $request)
    {
        $user = $request->user();

        $apotekaId = $user->isCentralniAdmin()
            ? $request->input('apoteka_id')
            : $user->apoteka_id;

        $od = $request->input('od', now()->startOfMonth()->toDateString());
        $do = $request->input('do', now()->toDateString());
        $limit = $request->input('limit', 20);

        $report = $this->reportService->getMostRequestedMedicines($limit, $apotekaId, $od, $do);

        $apoteke = $user->isCentralniAdmin()
            ? Apoteka::where('aktivna', true)->orderBy('naziv')->get()
            : collect();

        return view('reports.lekovi', compact('report', 'apoteke', 'apotekaId', 'od', 'do', 'limit'));
    }

    public function recepti(Request $request)
    {
        $od = $request->input('od', now()->startOfMonth()->toDateString());
        $do = $request->input('do', now()->toDateString());

        $report = $this->reportService->getPrescriptionReport($od, $do);

        return view('reports.recepti', compact('report', 'od', 'do'));
    }

    public function dobavljaci(Request $request)
    {
        $dobavljacId = $request->input('dobavljac_id');
        $od = $request->input('od', now()->startOfMonth()->toDateString());
        $do = $request->input('do', now()->toDateString());

        $report = $this->reportService->getSupplierReport($dobavljacId, $od, $do);

        $dobavljaci = Dobavljac::where('aktivan', true)->orderBy('naziv')->get();

        return view('reports.dobavljaci', compact('report', 'dobavljaci', 'dobavljacId', 'od', 'do'));
    }
}
