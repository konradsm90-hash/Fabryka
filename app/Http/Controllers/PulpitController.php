<?php

namespace App\Http\Controllers;

use App\Models\Pracownik;
use App\Models\Nieobecnosc;
use App\Models\OdbicieRcp;
use Carbon\Carbon;

class PulpitController extends Controller
{
    public function index()
    {
        $dzisiaj = Carbon::today()->format('Y-m-d');

        // 1. Statystyki KPI
        $liczbaPracownikow = Pracownik::where('czy_aktywny', true)->count();

        $liczbaNieobecnychDzisiaj = Nieobecnosc::where('status', 'ZATWIERDZONE')
            ->where('data_od', '<=', $dzisiaj)
            ->where('data_do', '>=', $dzisiaj)
            ->count();

        // Wyliczenie obecnych dzisiaj w pracy
        $obecniDzisiaj = max(0, $liczbaPracownikow - $liczbaNieobecnychDzisiaj);

        // Wyliczenie % frekwencji
        $procentObecnosci = $liczbaPracownikow > 0 
            ? round(($obecniDzisiaj / $liczbaPracownikow) * 100, 1) 
            : 100;

        // Podgląd ostatnich nieobecnych
        $nieobecniDzisiaj = Nieobecnosc::with('pracownik')
            ->where('status', 'ZATWIERDZONE')
            ->where('data_od', '<=', $dzisiaj)
            ->where('data_do', '>=', $dzisiaj)
            ->limit(5)
            ->get();

        // 2. Anomalie RCP
        $anomalieRcp = OdbicieRcp::with('pracownik')
            ->whereDate('czas_odbicia', $dzisiaj)
            ->where('typ', 'WEJSCIE')
            ->whereNotIn('pracownik_id', function ($query) use ($dzisiaj) {
                $query->select('pracownik_id')
                    ->from('odbicia_rcp')
                    ->whereDate('czas_odbicia', $dzisiaj)
                    ->where('typ', 'WYJSCIE');
            })
            ->limit(5)
            ->get();

        // 3. Wnioski oczekujące na akceptację
        $oczekujaceWnioski = Nieobecnosc::with('pracownik')
            ->where('status', 'OCZEKUJE')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'liczbaPracownikow',
            'obecniDzisiaj',
            'liczbaNieobecnychDzisiaj',
            'procentObecnosci',
            'nieobecniDzisiaj',
            'anomalieRcp',
            'oczekujaceWnioski'
        ));
    }
}