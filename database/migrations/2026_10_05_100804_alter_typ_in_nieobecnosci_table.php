<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nieobecnosci', function (Blueprint $table) {
            $table->string('typ', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('nieobecnosci', function (Blueprint $table) {
            $table->string('typ', 20)->change();
        });
    }
};