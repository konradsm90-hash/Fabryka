<?php

namespace App\Http\Controllers;

use App\Models\Pracownik;
use App\Services\GrafikService;
use Illuminate\Http\Request;

class GrafikController extends Controller
{
    public function index(Request $request, GrafikService $grafikService)
    {
        $pracownicy = Pracownik::where('czy_aktywny', true)->get();

        $pracownikId = $request->input('pracownik_id', $pracownicy->first()?->id);
        $rok = (int) $request->input('rok', 2025);
        $miesiac = (int) $request->input('miesiac', 1);

        $wybranyPracownik = $pracownikId ? Pracownik::find($pracownikId) : null;
        $analiza = null;

        if ($wybranyPracownik) {
            $analiza = $grafikService->analizujAnomalie($wybranyPracownik->id, $rok, $miesiac);
        }

        return view('grafik.index', compact(
            'pracownicy',
            'wybranyPracownik',
            'analiza',
            'rok',
            'miesiac'
        ));
    }
}