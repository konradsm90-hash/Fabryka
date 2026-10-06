<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pracownik extends Model
{
    protected $table = 'pracownicy';

    protected $fillable = [
        'numer_kart_rcp',
        'imie',
        'nazwisko',
        'pesel',
        'wymiar_urlopu',
        'zaklad_id',
        'dzial_id',
        'stanowisko_id',
        'czy_aktywny'
    ];

    public function nieobecnosci()
    {
        return $this->hasMany(Nieobecnosc::class, 'pracownik_id');
    }

    public function historiaStawek()
    {
        return $this->hasMany(HistoriaStawki::class, 'pracownik_id');
    }

    public function pobierzStawkeNaDzien(string $data): float
    {
        $wpis = $this->historiaStawek()
            ->where('od_daty', '<=', $data)
            ->where(function ($query) use ($data) {
                $query->whereNull('do_daty')->orWhere('do_daty', '>=', $data);
            })
            ->orderBy('od_daty', 'desc')
            ->first();

        return $wpis ? (float) $wpis->stawka_godzinowa : 0.00;
    }

    public function pobierzWykorzystanyUrlop(?int $rok = null): int
    {
        $rok = $rok ?? date('Y');

        return (int) $this->nieobecnosci()
            ->where('status', 'ZATWIERDZONE')
            ->whereIn('typ', ['URLOP_WYPOCZYNKOWY', 'URLOP_NA_ZADANIE'])
            ->whereYear('data_od', $rok)
            ->sum('liczba_dni_roboczych');
    }

    public function pobierzPozostalyUrlop(?int $rok = null): int
    {
        return ($this->wymiar_urlopu ?? 26) - $this->pobierzWykorzystanyUrlop($rok);
    }
}