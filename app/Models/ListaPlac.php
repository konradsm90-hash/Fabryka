<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaPlac extends Model
{
    protected $table = 'listy_plac';

    protected $fillable = [
        'rok',
        'miesiac',
        'zaklad_id',
        'status',
        'suma_brutto',
        'data_zamkniecia'
    ];

    public function pozycje()
    {
        return $this->hasMany(PozycjaListyPlac::class, 'lista_plac_id');
    }
}