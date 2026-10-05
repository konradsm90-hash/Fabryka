<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlacaController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KorektaController;
use App\Http\Controllers\NieobecnoscController;
use App\Http\Controllers\RaportController;

Route::get('/', function () {
    return redirect()->route('listy-plac.index');
});

// Listy płac
Route::get('/listy-plac', [PlacaController::class, 'index'])->name('listy-plac.index');
Route::get('/listy-plac/{id}', [PlacaController::class, 'show'])->name('listy-plac.show');
Route::post('/listy-plac/przelicz', [PlacaController::class, 'przelicz'])->name('listy-plac.przelicz');

// Grafik & Anomalie
Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik.index');

// Korekty RCP
Route::get('/korekty', [KorektaController::class, 'index'])->name('korekty.index');
Route::post('/korekty', [KorektaController::class, 'store'])->name('korekty.store');

// Nieobecności & Urlopy
Route::get('/nieobecnosci', [NieobecnoscController::class, 'index'])->name('nieobecnosci.index');
Route::post('/nieobecnosci', [NieobecnoscController::class, 'store'])->name('nieobecnosci.store');

// Raporty i Zestawienia
Route::get('/raporty', [RaportController::class, 'index'])->name('raporty.index');