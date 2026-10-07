<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('receptury', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->text('opis')->nullable();
            $table->timestamps();
        });

        Schema::create('receptury_kroki', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receptura_id')->constrained('receptury')->onDelete('cascade');
            $table->integer('kolejnosc');
            $table->string('nazwa_zadania');
            $table->text('opis_wykonania')->nullable();
            $table->integer('szacowany_czas_minut')->default(60);
        });

        Schema::create('zlecenia_produkcyjne', function (Blueprint $table) {
            $table->id();
            $table->string('kod_zlecenia')->unique();
            $table->foreignId('receptura_id')->constrained('receptury');
            $table->integer('ilosc_sztuk')->default(1);
            $table->enum('status', ['PLANOWANE', 'W_TRAKCIE', 'ZAKONCZONE', 'ANULOWANE'])->default('PLANOWANE');
            $table->date('termin_realizacji');
            $table->timestamps();
        });

        Schema::create('zadania_produkcyjne', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zlecenie_id')->constrained('zlecenia_produkcyjne')->onDelete('cascade');
            $table->foreignId('pracownik_id')->nullable()->constrained('pracownicy')->onDelete('set null');
            $table->string('nazwa');
            $table->text('opis')->nullable();
            $table->enum('status', ['DO_ZROBIENIA', 'W_TRAKCIE', 'KONTROLA', 'ZAKONCZONE'])->default('DO_ZROBIENIA');
            $table->integer('kolejnosc')->default(0);
            $table->integer('szacowany_czas_minut')->default(60);
            $table->timestamps();
        });

        Schema::create('zadania_komentarze', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zadanie_id')->constrained('zadania_produkcyjne')->onDelete('cascade');
            $table->foreignId('pracownik_id')->constrained('pracownicy');
            $table->text('tresc');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('zadania_komentarze');
        Schema::dropIfExists('zadania_produkcyjne');
        Schema::dropIfExists('zlecenia_produkcyjne');
        Schema::dropIfExists('receptury_kroki');
        Schema::dropIfExists('receptury');
    }
};