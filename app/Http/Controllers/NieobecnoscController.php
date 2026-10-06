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

        $od = \Carbon\Carbon::parse($request->data_od);
        $do = \Carbon\Carbon::parse($request->data_do);

        $dniRobocze = $od->diffInDaysFiltered(function (\Carbon\Carbon $date) {
            return !$date->isWeekend();
        }, $do->copy()->addDay());

        Nieobecnosc::create([
            'pracownik_id' => $request->pracownik_id,
            'typ' => $request->typ,
            'data_od' => $request->data_od,
            'data_do' => $request->data_do,
            'liczba_dni_roboczych' => $dniRobocze,
            'status' => 'OCZEKUJE', // Nowe wnioski trafiają ze statusem OCZEKUJE
        ]);

        return redirect()->back()->with('success', 'Wniosek o nieobecność został zarejestrowany i oczekuje na decyzję.');
    }

    public function zatwierdz($id)
    {
        Nieobecnosc::where('id', $id)->update(['status' => 'ZATWIERDZONE']);
        return back()->with('success', 'Wniosek został zatwierdzony.');
    }

    public function odrzuc($id)
    {
        Nieobecnosc::where('id', $id)->update(['status' => 'ODRZUCONE']);
        return back()->with('success', 'Wniosek został odrzucony.');
    }

    public function masoweAkcje(Request $request)
    {
        $ids = $request->input('ids', []);
        $akcja = $request->input('akcja');

        if (empty($ids)) {
            return back()->with('success', 'Nie zaznaczono żadnych pozycji.');
        }

        if ($akcja === 'ZATWIERDZ') {
            Nieobecnosc::whereIn('id', $ids)->update(['status' => 'ZATWIERDZONE']);
        } elseif ($akcja === 'ODRZUC') {
            Nieobecnosc::whereIn('id', $ids)->update(['status' => 'ODRZUCONE']);
        } elseif ($akcja === 'USUN') {
            Nieobecnosc::whereIn('id', $ids)->delete();
        }

        return back()->with('success', 'Pomyślnie wykonano masową akcję dla ' . count($ids) . ' elementów.');
    }
}