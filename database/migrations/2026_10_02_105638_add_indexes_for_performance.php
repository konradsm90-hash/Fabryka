<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('odbicia_rcp', function (Blueprint $table) {
            $table->index('czas_odbicia', 'idx_odbicia_sam_czas');
        });

        Schema::table('listy_plac', function (Blueprint $table) {
            $table->index(['rok', 'miesiac'], 'idx_listy_rok_miesiac');
        });

        Schema::table('nieobecnosci', function (Blueprint $table) {
            $table->index(['pracownik_id', 'data_od', 'data_do'], 'idx_nieobecnosci_daty');
        });
    }

    public function down(): void {}
};