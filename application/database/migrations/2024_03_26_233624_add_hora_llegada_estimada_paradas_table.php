<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('paradas', function (Blueprint $table) {
            $table->dateTime("hora_llegada_estimada")->after("orden")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paradas', function (Blueprint $table) {
            $table->dropColumn("hora_llegada_estimada");
        });
    }
};
