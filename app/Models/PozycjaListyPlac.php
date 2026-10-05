<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PozycjaListyPlac extends Model
{
    protected $table = 'pozycje_listy_plac';

    protected $fillable = [
        'lista_plac_id',
        'pracownik_id',
        'godziny_podstawowe',
        'godziny_nadgodziny_50',
        'godziny_nadgodziny_100',
        'godziny_nocne',
        'godziny_niedziele_swieta',
        'zastosowana_stawka_bazowa',
        'kwota_podstawowa',
        'kwota_nadgodziny',
        'kwota_dodatek_nocny',
        'kwota_swieta',
        'kwota_korekty_retro',
        'kwota_brutto_razem'
    ];

    public function listaPlac()
    {
        return $this->belongsTo(ListaPlac::class, 'lista_plac_id');
    }

    public function pracownik()
    {
        return $this->belongsTo(Pracownik::class, 'pracownik_id');
    }
}