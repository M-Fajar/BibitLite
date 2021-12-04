<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MaintenanceService;

class MaintenanceController extends Controller
{
    public function index(MaintenanceService $maintenanceService)
    {
        if ($maintenanceService->hasMaintenance()) {
            return abort(503, $maintenanceService->getNotice());
        }
        return redirect('/');
    }
}
