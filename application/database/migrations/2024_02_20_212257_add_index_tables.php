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
            $table->index(['direccion_formateada']);
            $table->index(['created_at']);
            $table->index(['realizado_en']);
        });

        Schema::table('recorridos', function (Blueprint $table) {
            $table->index(['finalizado']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->index(['track_id']);
            $table->index(['created_at']);
            $table->index(['gestionado']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->index(['nombre']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
