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
        Schema::table('usuarios_empresas', function (Blueprint $table) {
            $table->unsignedBigInteger('invitacion_id')->nullable()->after('rol_id');
            $table->foreign('invitacion_id')->references('id')->on('invitaciones_empresas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios_empresas', function (Blueprint $table) {
            $table->dropForeign(['invitacion_id']);
        });
    }
};
