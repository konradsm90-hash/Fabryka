<?php

namespace App\Services;

use App\Models\OdbicieRcp;
use Carbon\Carbon;

class GrafikService
{
    public function analizujAnomalie(int $pracownikId, int $rok = 2025, int $miesiac = 1): array
    {
        $poczatek = Carbon::createFromDate($rok, $miesiac, 1)->startOfMonth();
        $koniec = $poczatek->copy()->endOfMonth();

  
        $odbicia = OdbicieRcp::where('pracownik_id', $pracownikId)
            ->whereBetween('czas_odbicia', [$poczatek, $koniec])
            ->orderBy('czas_odbicia', 'asc')
            ->get();

        $spoznieniaMinuty = 0;
        $anomalie = [];

    
        $odbiciaWgDni = $odbicia->groupBy(function ($odbicie) {
            return Carbon::parse($odbicie->czas_odbicia)->format('Y-m-d');
        });

        foreach ($odbiciaWgDni as $data => $puncze) {
          
            if ($puncze->count() % 2 !== 0) {
                $anomalie[] = [
                    'typ' => 'Niezbilansowane odbicia',
                    'opis' => 'Nieparzysta liczba zdarzeń na czytniku (prawdopodobny brak wejścia lub wyjścia)',
                    'data' => $data,
                ];
            }

       
            $pierwszeOdbicie = Carbon::parse($puncze->first()->czas_odbicia);
            $planowaneWejscie = Carbon::parse($data . ' 08:00:00');

            if ($pierwszeOdbicie->gt($planowaneWejscie)) {
                $roznicaMinut = (int) $pierwszeOdbicie->diffInMinutes($planowaneWejscie);
                if ($roznicaMinut > 5) { 
                    $spoznieniaMinuty += $roznicaMinut;
                    $anomalie[] = [
                        'typ' => 'Spóźnienie',
                        'opis' => "Przyjście do pracy o {$pierwszeOdbicie->format('H:i')} (spóźnienie o {$roznicaMinut} min)",
                        'data' => $data,
                    ];
                }
            }
        }

        return [
            'spoznienia_minuty' => $spoznieniaMinuty,
            'anomalie' => $anomalie,
        ];
    }
}