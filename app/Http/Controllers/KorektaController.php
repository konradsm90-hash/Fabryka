<?php

namespace App\Http\Controllers;

use App\Models\Pracownik;
use App\Models\OdbicieRcp;
use App\Services\KorektaService;
use Illuminate\Http\Request;

class KorektaController extends Controller
{
    public function index()
    {
        $pracownicy = Pracownik::where('czy_aktywny', true)->take(50)->get();
        $ostatnieOdbicia = OdbicieRcp::with('pracownik')->orderBy('czas_odbicia', 'desc')->take(20)->get();

        return view('korekty.index', compact('pracownicy', 'ostatnieOdbicia'));
    }

    public function store(Request $request, KorektaService $korektaService)
    {
        $request->validate([
            'pracownik_id' => 'required|exists:pracownicy,id',
            'typ' => 'required|in:WEJSCIE,WYJSCIE,PRZERWA_START,PRZERWA_STOP',
            'czas_odbicia' => 'required|date',
            'powod' => 'required|string|max:255',
        ]);

        $korektaService->dodajLubPoprawOdbicie(
            $request->pracownik_id,
            $request->czas_odbicia,
            $request->typ,
            $request->powod,
            1 // ID kierownika
        );

        return redirect()->back()->with('success', 'Korekta została pomyślnie dodana i zapisana w śladzie audytowym.');
    }
}