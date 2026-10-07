<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukcjaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Przykładowa Receptura
        $recepturaId = DB::table('receptury')->insertGetId([
            'nazwa' => 'Rama Stalowa Typ-A',
            'opis' => 'Standardowa rama konstrukcyjna hal produkcyjnych',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Kroki produkcyjne dla receptury
        DB::table('receptury_kroki')->insert([
            [
                'receptura_id' => $recepturaId,
                'kolejnosc' => 1,
                'nazwa_zadania' => 'Cięcie profili stalowych',
                'opis_wykonania' => 'Dociąć profile 40x40mm na długość 2000mm według rysunku T-12',
                'szacowany_czas_minut' => 30,
            ],
            [
                'receptura_id' => $recepturaId,
                'kolejnosc' => 2,
                'nazwa_zadania' => 'Spawanie ramy głównej',
                'opis_wykonania' => 'Spawanie metodą MAG. Sprawdzić przekątne przed zespawaniem.',
                'szacowany_czas_minut' => 90,
            ],
            [
                'receptura_id' => $recepturaId,
                'kolejnosc' => 3,
                'nazwa_zadania' => 'Malowanie proszkowe',
                'opis_wykonania' => 'Kolor RAL 7016 (Antracyt). Powłoka min. 80 mikronów.',
                'szacowany_czas_minut' => 60,
            ],
        ]);
    }
}