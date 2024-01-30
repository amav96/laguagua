<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes_numeros', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('codigo_area_id')->nullable();
            $table->foreign('codigo_area_id')->references('id')->on('codigos_area');

            $table->string('numero')->nullable();

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['codigo_area_id']);
        });

            Schema::table('clientes', function (Blueprint $table) {
                
                $table->dropColumn('codigo_area_id');

                $table->dropColumn('numero_celular');
                $table->dropColumn('numero_fijo');
            });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('codigos_area', function (Blueprint $table) {
            $table->dropForeign(['codigo_area_id']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
        });

        Schema::dropIfExists('clientes_numeros');
    }
};
