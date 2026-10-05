<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RaportService
{
    public function pobierzKosztyMiesiaca(int $rok, int $miesiac)
    {
        $query = DB::table('listy_plac')
            ->join('pozycje_listy_plac', 'listy_plac.id', '=', 'pozycje_listy_plac.lista_plac_id')
            ->join('pracownicy', 'pozycje_listy_plac.pracownik_id', '=', 'pracownicy.id')
            ->where('listy_plac.rok', $rok)
            ->where('listy_plac.miesiac', $miesiac);

        if (Schema::hasColumn('pracownicy', 'zaklad') && Schema::hasColumn('pracownicy', 'dzial')) {
            return $query->select(
                'pracownicy.zaklad',
                'pracownicy.dzial',
                DB::raw('COUNT(DISTINCT pracownicy.id) as liczba_pracownikow'),
                DB::raw('SUM(pozycje_listy_plac.kwota_brutto_razem) as suma_brutto')
            )
            ->groupBy('pracownicy.zaklad', 'pracownicy.dzial')
            ->get();
        }

      
        return $query->select(
            DB::raw("'Zakład Główny' as zaklad"),
            DB::raw("'Produkcja / Dział Operacyjny' as dzial"),
            DB::raw('COUNT(DISTINCT pracownicy.id) as liczba_pracownikow'),
            DB::raw('SUM(pozycje_listy_plac.kwota_brutto_razem) as suma_brutto')
        )
        ->get();
    }

    public function pobierzRaportNadgodzin(int $rok, int $miesiac)
    {
        return DB::table('listy_plac')
            ->join('pozycje_listy_plac', 'listy_plac.id', '=', 'pozycje_listy_plac.lista_plac_id')
            ->join('pracownicy', 'pozycje_listy_plac.pracownik_id', '=', 'pracownicy.id')
            ->select(
                'pracownicy.imie',
                'pracownicy.nazwisko',
                DB::raw('(pozycje_listy_plac.godziny_nadgodziny_50 + pozycje_listy_plac.godziny_nadgodziny_100) as godziny_nadgodziny'),
                'pozycje_listy_plac.kwota_brutto_razem'
            )
            ->where('listy_plac.rok', $rok)
            ->where('listy_plac.miesiac', $miesiac)
            ->whereRaw('(pozycje_listy_plac.godziny_nadgodziny_50 + pozycje_listy_plac.godziny_nadgodziny_100) > 0')
            ->get();
    }
}