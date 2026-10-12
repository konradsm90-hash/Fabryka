<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlacaController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KorektaController;
use App\Http\Controllers\NieobecnoscController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\PulpitController;
use App\Http\Controllers\ProdukcjaController;
use App\Http\Controllers\RekrutacjaController;

// Awaryjna trasa logowania
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

// Sekcja Produkcja / MES
Route::get('/produkcja/kanban', [ProdukcjaController::class, 'kanban'])->name('kanban.index');
Route::post('/produkcja/zlecenia/generuj', [ProdukcjaController::class, 'generujZlecenie'])->name('zlecenia.generuj');
Route::post('/produkcja/kanban/{id}/przypisz', [ProdukcjaController::class, 'przypiszPracownika'])->name('kanban.przypisz');

Route::get('/moje-zadania', [ProdukcjaController::class, 'mojeZadania'])->name('pracownik.zadania');
Route::post('/produkcja/kanban/{id}/status', [ProdukcjaController::class, 'zmienStatus'])->name('kanban.status');
Route::post('/produkcja/kanban/{id}/komentarz', [ProdukcjaController::class, 'dodajKomentarz'])->name('kanban.komentarz');

// Formularz rekrutacji dla kandydatów
Route::get('/rekrutacja', [RekrutacjaController::class, 'index'])->name('rekrutacja.form');
Route::post('/rekrutacja', [RekrutacjaController::class, 'store'])->name('rekrutacja.store');

// Panel menedżera (z pełnymi aliasami logowania, obsługującymi zarówno menadzer.loguj jak i menadzer.zaloguj)
Route::get('/menadzer/login', [RekrutacjaController::class, 'pokazLogowanie'])->name('menadzer.login');
Route::post('/menadzer/login', [RekrutacjaController::class, 'loguj'])->name('menadzer.loguj');
Route::post('/menadzer/zaloguj', [RekrutacjaController::class, 'loguj'])->name('menadzer.zaloguj');
Route::get('/menadzer/panel', [RekrutacjaController::class, 'panel'])->name('menadzer.panel');
Route::post('/menadzer/wyloguj', [RekrutacjaController::class, 'wyloguj'])->name('menadzer.wyloguj');

// Panel menedżera
Route::get('/menadzer/login', [RekrutacjaController::class, 'pokazLogowanie'])->name('menadzer.login');
Route::post('/menadzer/login', [RekrutacjaController::class, 'loguj'])->name('menadzer.loguj');
Route::post('/menadzer/zaloguj', [RekrutacjaController::class, 'loguj'])->name('menadzer.zaloguj');
Route::get('/menadzer/panel', [RekrutacjaController::class, 'panel'])->name('menadzer.panel');
Route::post('/menadzer/wyloguj', [RekrutacjaController::class, 'wyloguj'])->name('menadzer.wyloguj');
Route::get('/menadzer/kandydat/{id}/cv', [RekrutacjaController::class, 'pobierzCv'])->name('menadzer.cv'); // <-- Nowa trasa do pobierania CV