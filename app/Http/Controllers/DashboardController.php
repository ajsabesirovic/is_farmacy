<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Services\InventoryService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected InventoryService $inventoryService;
    protected ReportService $reportService;

    public function __construct(InventoryService $inventoryService, ReportService $reportService)
    {
        $this->inventoryService = $inventoryService;
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        return match($user->tip) {
            UserType::FARMACEUT => $this->farmaceutDashboard($user),
            UserType::ADMIN_APOTEKE => $this->adminApotekeDashboard($user),
            UserType::CENTRALNI_ADMIN => $this->centralniAdminDashboard($user),
            UserType::REGISTROVANI_KORISNIK => $this->registrovaniKorisnikDashboard($user),
        };
    }

    protected function farmaceutDashboard($user)
    {
        $apotekaId = $user->apoteka_id;

        $data = [
            'niske_zalihe' => $this->inventoryService->getLowStockItems($apotekaId)->take(5),
            'danas_prodaja' => $user->apoteka->prodaje()
                                   ->whereDate('datum', today())
                                   ->count(),
        ];

        return view('dashboard.farmaceut', $data);
    }

    protected function adminApotekeDashboard($user)
    {
        $apotekaId = $user->apoteka_id;

        $data = [
            'niske_zalihe' => $this->inventoryService->getLowStockItems($apotekaId),
            'inventar_summary' => $this->reportService->getInventorySummary($apotekaId),
            'mesecna_prodaja' => $this->reportService->getSalesReport(
                $apotekaId,
                now()->startOfMonth()->toDateString(),
                now()->toDateString()
            ),
            'najtrazeniji_lekovi' => $this->reportService->getMostRequestedMedicines(5, $apotekaId),
        ];

        return view('dashboard.admin-apoteke', $data);
    }

    protected function centralniAdminDashboard($user)
    {
        $data = [
            'niske_zalihe' => $this->inventoryService->getLowStockItems(),
            'inventar_summary' => $this->reportService->getInventorySummary(),
            'najtrazeniji_lekovi' => $this->reportService->getMostRequestedMedicines(10),
            'recept_report' => $this->reportService->getPrescriptionReport(
                now()->startOfMonth()->toDateString(),
                now()->toDateString()
            ),
        ];

        return view('dashboard.centralni-admin', $data);
    }

    protected function registrovaniKorisnikDashboard($user)
    {
        return redirect()->route('pretraga');
    }
}
