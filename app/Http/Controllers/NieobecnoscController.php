<?php

namespace App\Http\Controllers;

use App\Models\Pracownik;
use App\Models\Nieobecnosc;
use Illuminate\Http\Request;

class NieobecnoscController extends Controller
{
    public function index()
    {
        $pracownicy = Pracownik::where('czy_aktywny', true)->take(50)->get();
        $nieobecnosci = Nieobecnosc::with('pracownik')->orderBy('data_od', 'desc')->take(20)->get();

        return view('nieobecnosci.index', compact('pracownicy', 'nieobecnosci'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pracownik_id' => 'required|exists:pracownicy,id',
            'typ' => 'required|in:URLOP_WYPOCZYNKOWY,ZWOLNIENIE_LEKARSKIE,URLOP_NA_ZADANIE,OKOLICZNOSCIOWY',
            'data_od' => 'required|date',
            'data_do' => 'required|date|after_or_equal:data_od',
        ]);

        Nieobecnosc::create([
            'pracownik_id' => $request->pracownik_id,
            'typ' => $request->typ,
            'data_od' => $request->data_od,
            'data_do' => $request->data_do,
            'status' => 'ZATWIERDZONE',
        ]);

        return redirect()->back()->with('success', 'Nieobecność została wprowadzona.');
    }
}