<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlacaController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KorektaController;
use App\Http\Controllers\NieobecnoscController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\PulpitController;
use App\Http\Controllers\ProdukcjaController;

// Awaryjna trasa logowania (zapobiega błędowi 'login not defined')
Route::get('/login', function() {
    return redirect('/produkcja/kanban');
})->name('login');

// Strona Główna / Pulpit
Route::get('/', [PulpitController::class, 'index'])->name('pulpit');

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

// Sekcja Produkcja / MES (dostępna bezpośrednio bez przekierowania na login)
Route::get('/produkcja/kanban', [ProdukcjaController::class, 'kanban'])->name('kanban.index');
Route::post('/produkcja/zlecenia/generuj', [ProdukcjaController::class, 'generujZlecenie'])->name('zlecenia.generuj');
Route::post('/produkcja/kanban/{id}/przypisz', [ProdukcjaController::class, 'przypiszPracownika'])->name('kanban.przypisz');

Route::get('/moje-zadania', [ProdukcjaController::class, 'mojeZadania'])->name('pracownik.zadania');
Route::post('/produkcja/kanban/{id}/status', [ProdukcjaController::class, 'zmienStatus'])->name('kanban.status');
Route::post('/produkcja/kanban/{id}/komentarz', [ProdukcjaController::class, 'dodajKomentarz'])->name('kanban.komentarz');