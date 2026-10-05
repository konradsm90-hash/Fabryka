<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriaStawki extends Model
{
    protected $table = 'historia_stawek';

    protected $fillable = [
        'pracownik_id',
        'stanowisko_id',
        'stawka_godzinowa',
        'od_daty',
        'do_daty'
    ];
}