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
        Schema::table('usuarios_consumos', function (Blueprint $table) {
            $table->integer("cantidad_polyline")->default(0);
            $table->double("consumo_polyline")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios_consumos', function (Blueprint $table) {
            $table->dropColumn("cantidad_polyline")->default(0);
            $table->dropColumn("consumo_polyline")->default(0);
        });
    }
};
