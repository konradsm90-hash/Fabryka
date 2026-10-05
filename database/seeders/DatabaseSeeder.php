<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('odbicia_rcp')->truncate();
        DB::table('pracownicy')->truncate();
        DB::table('stanowiska')->truncate();
        DB::table('dzialy')->truncate();
        DB::table('zaklady')->truncate();

        $this->command->info('Tworzenie słowników...');
        $zakladId = DB::table('zaklady')->insertGetId(['nazwa' => 'Zakład Katowice', 'kod_zakladu' => 'Z1', 'created_at' => now()]);
        $dzialId = DB::table('dzialy')->insertGetId(['zaklad_id' => $zakladId, 'nazwa' => 'Montaż', 'created_at' => now()]);
        $stanowiskoId = DB::table('stanowiska')->insertGetId(['nazwa' => 'Operator', 'domyslna_stawka_godzinowa' => 32.50, 'created_at' => now()]);

        $this->command->info('Generowanie 5 000 pracowników...');
        $pracownicyIds = [];

        for ($i = 1; $i <= 5000; $i++) {
            $pId = DB::table('pracownicy')->insertGetId([
                'numer_kart_rcp' => 'CARD_' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'imie' => 'Pracownik_' . $i,
                'nazwisko' => 'Testowy',
                'pesel' => '900101' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'zaklad_id' => $zakladId,
                'dzial_id' => $dzialId,
                'stanowisko_id' => $stanowiskoId,
                'czy_aktywny' => true,
                'created_at' => now()
            ]);

            DB::table('historia_stawek')->insert([
                'pracownik_id' => $pId,
                'stanowisko_id' => $stanowiskoId,
                'stawka_godzinowa' => 32.50,
                'od_daty' => '2025-01-01',
                'created_at' => now()
            ]);

            $pracownicyIds[] = $pId;
        }

        $this->command->info('Generowanie ~3,1 mln odbić za rok 2025...');
        $poczatek = Carbon::parse('2025-01-01');
        $koniec = Carbon::parse('2025-12-31');

        $paczka = [];
        $licznik = 0;

        foreach ($pracownicyIds as $pId) {
            $kursor = $poczatek->copy();
            while ($kursor->lte($koniec)) {
                if (!$kursor->isWeekend()) {
                    $paczka[] = [
                        'pracownik_id' => $pId,
                        'czas_odbicia' => $kursor->copy()->setTime(6, rand(0, 10))->format('Y-m-d H:i:s'),
                        'typ' => 'WEJSCIE',
                        'zrodlo' => 'CZYTNIK',
                        'numer_terminala' => 'TERM_01',
                        'created_at' => now()
                    ];
                    $paczka[] = [
                        'pracownik_id' => $pId,
                        'czas_odbicia' => $kursor->copy()->setTime(14, rand(0, 10))->format('Y-m-d H:i:s'),
                        'typ' => 'WYJSCIE',
                        'zrodlo' => 'CZYTNIK',
                        'numer_terminala' => 'TERM_02',
                        'created_at' => now()
                    ];
                    $licznik += 2;

                    if (count($paczka) >= 5000) {
                        DB::table('odbicia_rcp')->insert($paczka);
                        $paczka = [];
                    }
                }
                $kursor->addDay();
            }
        }

        if (count($paczka) > 0) {
            DB::table('odbicia_rcp')->insert($paczka);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info("Zakończono! Wstawiono $licznik odbić RCP.");
    }
}