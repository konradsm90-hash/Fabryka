<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nieobecnosc extends Model
{
    use HasFactory;

    protected $table = 'nieobecnosci';

    protected $fillable = [
        'pracownik_id',
        'typ',
        'data_od',
        'data_do',
        'status',
    ];

    public function pracownik()
    {
        return $this->belongsTo(Pracownik::class, 'pracownik_id');
    }
}