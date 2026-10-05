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
        'zaklad_id',
        'dzial_id',
        'stanowisko_id',
        'czy_aktywny'
    ];

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
}