<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZadanieProdukcyjne extends Model
{
    protected $table = 'zadania_produkcyjne';
    protected $guarded = [];

    public function zlecenie() {
        return $this->belongsTo(ZlecenieProdukcyjne::class, 'zlecenie_id');
    }

    public function pracownik() {
        return $this->belongsTo(Pracownik::class, 'pracownik_id');
    }

    public function komentarze() {
        return $this->hasMany(ZadanieKomentarz::class, 'zadanie_id')->latest();
    }
}