<?php

namespace App\Services;

use App\Models\Pracownik;
use App\Models\OdbicieRcp;
use Carbon\Carbon;

class KalkulatorCzasuPracyService
{
    public function przeliczMiesiac(Pracownik $pracownik, int $rok, int $miesiac): array
    {
        $poczatek = Carbon::createFromDate($rok, $miesiac, 1)->startOfMonth();
        $koniec = $poczatek->copy()->endOfMonth();

        $odbicia = OdbicieRcp::where('pracownik_id', $pracownik->id)
            ->whereBetween('czas_odbicia', [$poczatek->copy()->subHours(12), $koniec->copy()->addHours(12)])
            ->orderBy('czas_odbicia', 'asc')
            ->get();

        $podstawowe = 0.0; $nadgodziny50 = 0.0; $nadgodziny100 = 0.0; $nocne = 0.0; $swieta = 0.0;
        $ostatnieWejscie = null;

        foreach ($odbicia as $o) {
            if ($o->typ === 'WEJSCIE') {
                $ostatnieWejscie = $o;
            } elseif ($o->typ === 'WYJSCIE' && $ostatnieWejscie) {
                $start = Carbon::parse($ostatnieWejscie->czas_odbicia);
                $stop = Carbon::parse($o->czas_odbicia);
                $godziny = $stop->diffInMinutes($start) / 60.0;

             
                $nocne += $this->obliczNocne($start, $stop);

                if ($start->isSunday()) {
                    $swieta += $godziny;
                }

                if ($godziny > 8.0) {
                    $podstawowe += 8.0;
                    $nadmiar = $godziny - 8.0;
                    if ($start->isSunday()) {
                        $nadgodziny100 += $nadmiar;
                    } else {
                        $nadgodziny50 += $nadmiar;
                    }
                } else {
                    $podstawowe += $godziny;
                }
                $ostatnieWejscie = null;
            }
        }

        return [
            'godziny_podstawowe' => round($podstawowe, 2),
            'godziny_nadgodziny_50' => round($nadgodziny50, 2),
            'godziny_nadgodziny_100' => round($nadgodziny100, 2),
            'godziny_nocne' => round($nocne, 2),
            'godziny_niedziele_swieta' => round($swieta, 2),
        ];
    }

    private function obliczNocne(Carbon $start, Carbon $stop): float
    {
        $minuty = 0;
        $kursor = $start->copy();
        while ($kursor->lt($stop)) {
            $h = $kursor->hour;
            if ($h >= 22 || $h < 6) { $minuty++; }
            $kursor->addMinute();
        }
        return round($minuty / 60.0, 2);
    }
}