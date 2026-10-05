<?php

namespace App\Http\Controllers;

use App\Services\RaportService;
use Illuminate\Http\Request;

class RaportController extends Controller
{
    public function index(RaportService $raportService)
    {
        $daneKoszty = $raportService->pobierzKosztyMiesiaca(2025, 1);
        $daneNadgodziny = $raportService->pobierzRaportNadgodzin(2025, 1);

        return view('raporty.index', compact('daneKoszty', 'daneNadgodziny'));
    }
}