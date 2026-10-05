<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdbicieRcp extends Model
{
    protected $table = 'odbicia_rcp';

    protected $fillable = [
        'pracownik_id',
        'czas_odbicia',
        'typ',
        'zrodlo',
        'numer_terminala',
        'czy_skorygowane'
    ];

    protected $casts = [
        'czas_odbicia' => 'datetime',
        'czy_skorygowane' => 'boolean'
    ];

    public function pracownik()
    {
        return $this->belongsTo(Pracownik::class, 'pracownik_id');
    }
}