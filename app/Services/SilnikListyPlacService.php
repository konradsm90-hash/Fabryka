<?php

namespace App\Services;

use App\Models\ListaPlac;
use App\Models\PozycjaListyPlac;
use App\Models\Pracownik;
use App\Models\OdbicieRcp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SilnikListyPlacService
{
    public function przeliczMiesiac(int $rok, int $miesiac)
    {
        $poczatek = Carbon::createFromDate($rok, $miesiac, 1)->startOfMonth();
        $koniec = $poczatek->copy()->endOfMonth();

        DB::transaction(function () use ($rok, $miesiac, $poczatek, $koniec) {

            $lista = ListaPlac::firstOrCreate(
                ['rok' => $rok, 'miesiac' => $miesiac],
                ['status' => 'SZKIC', 'suma_brutto' => 0]
            );


            PozycjaListyPlac::where('lista_plac_id', $lista->id)->delete();

            $nazwaTabeli = (new PozycjaListyPlac())->getTable();
            $istniejaceKolumny = Schema::getColumnListing($nazwaTabeli);
            $kolumnyDoWstawienia = array_values(array_filter($istniejaceKolumny, fn($col) => $col !== 'id'));

            $wszystkieOdbicia = OdbicieRcp::whereBetween('czas_odbicia', [$poczatek, $koniec])
                ->get()
                ->groupBy('pracownik_id');

            $sumaBruttoListy = 0;
            $now = now()->toDateTimeString();

     
            Pracownik::where('czy_aktywny', true)->chunk(500, function ($pracownicy) use ($wszystkieOdbicia, $lista, &$sumaBruttoListy, $now, $nazwaTabeli, $kolumnyDoWstawienia) {
                $pozycjeDoWstawienia = [];

                foreach ($pracownicy as $p) {
                    $odbiciaPracownika = $wszystkieOdbicia->get($p->id, collect());

        
                    if ($odbiciaPracownika->count() > 0) {
                        $godzinyPodstawowe = min(168, $odbiciaPracownika->count() * 4);
                        $godzinyNadgodziny = max(0, ($odbiciaPracownika->count() * 4) - 168);
                    } else {
                        $godzinyPodstawowe = 168;
                        $godzinyNadgodziny = 0;
                    }

                    $stawka = $p->stawka_godzinowa ?? 28.50;
                    $kwotaBrutto = ($godzinyPodstawowe * $stawka) + ($godzinyNadgodziny * $stawka * 1.5);
                    $sumaBruttoListy += $kwotaBrutto;


                    $rekord = [];
                    foreach ($kolumnyDoWstawienia as $kolumna) {
                        switch ($kolumna) {
                            case 'lista_plac_id':
                                $rekord[$kolumna] = $lista->id;
                                break;
                            case 'pracownik_id':
                                $rekord[$kolumna] = $p->id;
                                break;
                            case 'zastosowana_stawka_bazowa':
                            case 'stawka_godzinowa':
                            case 'stawka':
                                $rekord[$kolumna] = $stawka;
                                break;
                            case 'godziny_podstawowe':
                            case 'godziny_przepracowane':
                                $rekord[$kolumna] = $godzinyPodstawowe;
                                break;
                            case 'godziny_nadgodziny_50':
                            case 'godziny_nadgodziny':
                                $rekord[$kolumna] = $godzinyNadgodziny;
                                break;
                            case 'godziny_nadgodziny_100':
                            case 'godziny_nocne':
                            case 'kwota_korekty_retro':
                                $rekord[$kolumna] = 0;
                                break;
                            case 'kwota_brutto_razem':
                            case 'kwota_brutto':
                            case 'brutto':
                            case 'suma_brutto':
                                $rekord[$kolumna] = $kwotaBrutto;
                                break;
                            case 'kwota_netto':
                            case 'netto':
                                $rekord[$kolumna] = round($kwotaBrutto * 0.75, 2);
                                break;
                            case 'podatek':
                            case 'zaliczka_podatek':
                            case 'skladki_zus':
                            case 'zus':
                                $rekord[$kolumna] = 0;
                                break;
                            case 'created_at':
                            case 'updated_at':
                                $rekord[$kolumna] = $now;
                                break;
                            default:
                                $rekord[$kolumna] = 0;
                                break;
                        }
                    }

                    $pozycjeDoWstawienia[] = $rekord;
                }

                if (!empty($pozycjeDoWstawienia)) {
                    DB::table($nazwaTabeli)->insert($pozycjeDoWstawienia);
                }
            });

            $lista->update(['suma_brutto' => $sumaBruttoListy]);
        });
    }
}