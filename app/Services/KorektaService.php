<?php

namespace App\Services;

use App\Models\OdbicieRcp;
use App\Models\ListaPlac;
use App\Models\Pracownik;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KorektaService
{
    public function __construct(protected KalkulatorCzasuPracyService $kalkulator) {}

    public function poprawOdbicie(int $odbicieId, string $nowyCzas, string $powod, int $uzytkownikId): void
    {
        DB::transaction(function () use ($odbicieId, $nowyCzas, $powod, $uzytkownikId) {
            $odbicie = OdbicieRcp::findOrFail($odbicieId);
            $staryCzas = $odbicie->czas_odbicia;
            $pracownik = Pracownik::findOrFail($odbicie->pracownik_id);
            $data = Carbon::parse($staryCzas);

            $odbicie->update(['czas_odbicia' => $nowyCzas, 'czy_skorygowane' => true]);

            DB::table('korekty_odbic')->insert([
                'odbicie_rcp_id' => $odbicie->id,
                'pracownik_id' => $pracownik->id,
                'edytowal_uzytkownik_id' => $uzytkownikId,
                'stary_czas' => $staryCzas,
                'nowy_czas' => $nowyCzas,
                'powod_korekty' => $powod,
                'created_at' => now()
            ]);

            $lista = ListaPlac::where('rok', $data->year)->where('miesiac', $data->month)->where('status', 'ZAMKNIETA')->first();

            if ($lista) {
                $czas = $this->kalkulator->przeliczMiesiac($pracownik, $data->year, $data->month);
                $stawka = $pracownik->pobierzStawkeNaDzien($data->endOfMonth()->toDateString());
                $noweBrutto = ($czas['godziny_podstawowe'] * $stawka) + ($czas['godziny_nadgodziny_50'] * $stawka * 1.5);

                $staraPozycja = DB::table('pozycje_listy_plac')->where('lista_plac_id', $lista->id)->where('pracownik_id', $pracownik->id)->first();
                $roznica = round($noweBrutto - ($staraPozycja ? $staraPozycja->kwota_brutto_razem : 0), 2);

                if ($roznica != 0) {
                    DB::table('korekty_retroaktywne')->insert([
                        'pracownik_id' => $pracownik->id,
                        'pierwotny_rok' => $data->year,
                        'pierwotny_miesiac' => $data->month,
                        'roznica_kwota_brutto' => $roznica,
                        'opis_korekty' => $powod,
                        'rozliczono' => false,
                        'created_at' => now()
                    ]);
                }
            }
        });
    }
}