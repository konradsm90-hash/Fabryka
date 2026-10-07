<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZlecenieProdukcyjne extends Model
{
    protected $table = 'zlecenia_produkcyjne';
    protected $guarded = [];

    public function receptura() {
        return $this->belongsTo(Receptura::class);
    }

    public function zadania() {
        return $this->hasMany(ZadanieProdukcyjne::class, 'zlecenie_id');
    }
}