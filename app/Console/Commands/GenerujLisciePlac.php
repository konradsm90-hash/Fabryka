<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SilnikListyPlacService;

class GenerujLisciePlac extends Command
{
    protected $signature = 'rcp:generuj-liste {rok=2025} {miesiac=1}';
    protected $description = 'Przelicza i generuje listę płac';

    public function __construct(protected SilnikListyPlacService $silnik)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $rok = (int) $this->argument('rok');
        $miesiac = (int) $this->argument('miesiac');

        $this->info("Rozpoczynam wyliczanie listy płac za $miesiac/$rok...");
        $start = microtime(true);

        $lista = $this->silnik->wyliczMiesiac($rok, $miesiac);

        $czas = round(microtime(true) - $start, 2);

        $this->info("LISTA PŁAC WYGENEROWANA!");
        $this->table(
            ['ID Listy', 'Okres', 'Liczba pozycji', 'Suma Brutto (PLN)', 'Czas wykonania'],
            [[
                $lista->id, 
                "$miesiac/$rok", 
                $lista->pozycje()->count(), 
                number_format($lista->suma_brutto, 2, ',', ' ') . ' zł',
                "{$czas} s"
            ]]
        );
    }
}