<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pracownicy', function (Blueprint $table) {
            $table->integer('wymiar_urlopu')->default(26)->after('nazwisko');
        });
    }

    public function down(): void
    {
        Schema::table('pracownicy', function (Blueprint $table) {
            $table->dropColumn('wymiar_urlopu');
        });
    }
};