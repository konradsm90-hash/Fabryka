<?php

namespace App\Services;

use App\Models\OdbicieRcp;
use App\Models\Pracownik;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class RejestracjaOdbiciaService
{
    public function zarejestrujOdbicie(string $numerKarty, string $typ, string $numerTerminala, ?string $czas = null): OdbicieRcp
    {
        $czasOdbicia = $czas ? Carbon::parse($czas) : now();

        return DB::transaction(function () use ($numerKarty, $typ, $numerTerminala, $czasOdbicia) {
   
            $pracownik = Pracownik::where('numer_kart_rcp', $numerKarty)
                ->where('czy_aktywny', true)
                ->lockForUpdate()
                ->first();

            if (!$pracownik) {
                throw new Exception("Nie znaleziono aktywnej karty RCP: {$numerKarty}");
            }

  
            $ostatnieOdbicie = OdbicieRcp::where('pracownik_id', $pracownik->id)
                ->where('czas_odbicia', '>=', $czasOdbicia->copy()->subMinute())
                ->first();

            if ($ostatnieOdbicie) {
                throw new Exception("Zarejestrowano zbyt częste odbicie karty. Odczekaj chwilę.");
            }

            return OdbicieRcp::create([
                'pracownik_id' => $pracownik->id,
                'czas_odbicia' => $czasOdbicia,
                'typ' => $typ,
                'zrodlo' => 'CZYTNIK',
                'numer_terminala' => $numerTerminala,
                'czy_skorygowane' => false,
            ]);
        });
    }
}