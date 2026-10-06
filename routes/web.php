<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlacaController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KorektaController;
use App\Http\Controllers\NieobecnoscController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\ListaPlacController;
use App\Http\Controllers\PulpitController;


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

// Masowe akcje
Route::post('/nieobecnosci/masowe', [NieobecnoscController::class, 'masoweAkcje'])->name('nieobecnosci.masowe');
Route::post('/korekty/masowe', [KorektaController::class, 'masoweAkcje'])->name('korekty.masowe');
Route::post('/listy-plac/masowe', [PlacaController::class, 'masoweAkcje'])->name('listy-plac.masowe');

Route::post('/nieobecnosci/{id}/zatwierdz', [NieobecnoscController::class, 'zatwierdz'])->name('nieobecnosci.zatwierdz');
Route::post('/nieobecnosci/{id}/odrzuc', [NieobecnoscController::class, 'odrzuc'])->name('nieobecnosci.odrzuc');

Route::get('/', [PulpitController::class, 'index'])->name('pulpit');