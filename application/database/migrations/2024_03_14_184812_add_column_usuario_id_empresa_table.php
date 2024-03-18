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
        Schema::table('empresas', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->after('nombre')->nullable();
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('usuario_id');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
