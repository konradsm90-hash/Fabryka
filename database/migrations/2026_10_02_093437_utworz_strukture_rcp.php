<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zaklady', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->string('kod_zakladu')->unique();
            $table->timestamps();
        });

        Schema::create('dzialy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zaklad_id')->constrained('zaklady')->cascadeOnDelete();
            $table->string('nazwa');
            $table->timestamps();
        });

        Schema::create('stanowiska', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->decimal('domyslna_stawka_godzinowa', 8, 2);
            $table->timestamps();
        });

        Schema::create('pracownicy', function (Blueprint $table) {
            $table->id();
            $table->string('numer_kart_rcp')->unique();
            $table->string('imie');
            $table->string('nazwisko');
            $table->string('pesel')->unique();
            $table->foreignId('zaklad_id')->constrained('zaklady');
            $table->foreignId('dzial_id')->constrained('dzialy');
            $table->foreignId('stanowisko_id')->constrained('stanowiska');
            $table->boolean('czy_aktywny')->default(true);
            $table->timestamps();

            $table->index(['zaklad_id', 'dzial_id']);
        });

        Schema::create('historia_stawek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pracownik_id')->constrained('pracownicy')->cascadeOnDelete();
            $table->foreignId('stanowisko_id')->constrained('stanowiska');
            $table->decimal('stawka_godzinowa', 8, 2);
            $table->date('od_daty');
            $table->date('do_daty')->nullable();
            $table->timestamps();

            $table->index(['pracownik_id', 'od_daty', 'do_daty']);
        });

        Schema::create('grafiki_planowane', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pracownik_id')->constrained('pracownicy')->cascadeOnDelete();
            $table->date('data');
            $table->time('godzina_rozpoczecia');
            $table->time('godzina_zakonczenia');
            $table->integer('planowany_czas_minuty');
            $table->timestamps();

            $table->unique(['pracownik_id', 'data']);
        });

        Schema::create('odbicia_rcp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pracownik_id')->constrained('pracownicy')->cascadeOnDelete();
            $table->dateTime('czas_odbicia');
            $table->enum('typ', ['WEJSCIE', 'WYJSCIE', 'PRZERWA_START', 'PRZERWA_STOP']);
            $table->enum('zrodlo', ['CZYTNIK', 'MANUALNY_KIEROWNIK', 'SYSTEM']);
            $table->string('numer_terminala')->nullable();
            $table->boolean('czy_skorygowane')->default(false);
            $table->timestamps();

            $table->index(['pracownik_id', 'czas_odbicia']);
            $table->index('czas_odbicia');
        });

        Schema::create('nieobecnosci', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pracownik_id')->constrained('pracownicy')->cascadeOnDelete();
            $table->enum('typ', ['URLOP_WYPOCZYNKOWY', 'ZWOLNIENIE_L4', 'URLOP_BEZPLATNY']);
            $table->date('data_od');
            $table->date('data_do');
            $table->integer('liczba_dni_roboczych');
            $table->timestamps();
        });

        Schema::create('korekty_odbic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odbicie_rcp_id')->nullable()->constrained('odbicia_rcp')->nullOnDelete();
            $table->foreignId('pracownik_id')->constrained('pracownicy');
            $table->foreignId('edytowal_uzytkownik_id')->constrained('users');
            $table->dateTime('stary_czas')->nullable();
            $table->dateTime('nowy_czas');
            $table->string('stary_typ')->nullable();
            $table->string('nowy_typ');
            $table->text('powod_korekty');
            $table->timestamps();
        });

        Schema::create('listy_plac', function (Blueprint $table) {
            $table->id();
            $table->integer('rok');
            $table->integer('miesiac');
            $table->foreignId('zaklad_id')->nullable()->constrained('zaklady');
            $table->enum('status', ['SZKIC', 'ZAMKNIETA'])->default('SZKIC');
            $table->decimal('suma_brutto', 12, 2)->default(0.00);
            $table->timestamp('data_zamkniecia')->nullable();
            $table->timestamps();

            $table->unique(['rok', 'miesiac', 'zaklad_id']);
        });

        Schema::create('pozycje_listy_plac', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_plac_id')->constrained('listy_plac')->cascadeOnDelete();
            $table->foreignId('pracownik_id')->constrained('pracownicy');
            $table->decimal('godziny_podstawowe', 8, 2);
            $table->decimal('godziny_nadgodziny_50', 8, 2);
            $table->decimal('godziny_nadgodziny_100', 8, 2);
            $table->decimal('godziny_nocne', 8, 2);
            $table->decimal('godziny_niedziele_swieta', 8, 2);
            $table->decimal('zastosowana_stawka_bazowa', 8, 2);
            $table->decimal('kwota_podstawowa', 10, 2);
            $table->decimal('kwota_nadgodziny', 10, 2);
            $table->decimal('kwota_dodatek_nocny', 10, 2);
            $table->decimal('kwota_swieta', 10, 2);
            $table->decimal('kwota_korekty_retro', 10, 2)->default(0.00);
            $table->decimal('kwota_brutto_razem', 10, 2);
            $table->timestamps();

            $table->unique(['lista_plac_id', 'pracownik_id']);
        });

        Schema::create('korekty_retroaktywne', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pracownik_id')->constrained('pracownicy');
            $table->integer('pierwotny_rok');
            $table->integer('pierwotny_miesiac');
            $table->decimal('roznica_kwota_brutto', 10, 2);
            $table->text('opis_korekty');
            $table->boolean('rozliczono')->default(false);
            $table->foreignId('rozliczono_w_liscie_plac_id')->nullable()->constrained('listy_plac');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korekty_retroaktywne');
        Schema::dropIfExists('pozycje_listy_plac');
        Schema::dropIfExists('listy_plac');
        Schema::dropIfExists('korekty_odbic');
        Schema::dropIfExists('nieobecnosci');
        Schema::dropIfExists('odbicia_rcp');
        Schema::dropIfExists('grafiki_planowane');
        Schema::dropIfExists('historia_stawek');
        Schema::dropIfExists('pracownicy');
        Schema::dropIfExists('stanowiska');
        Schema::dropIfExists('dzialy');
        Schema::dropIfExists('zaklady');
    }
};