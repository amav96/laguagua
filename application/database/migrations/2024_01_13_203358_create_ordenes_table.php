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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rider_id')->nullable();
            $table->foreign('rider_id')->references('id')->on('usuarios');

            $table->unsignedBigInteger('operador_id');
            $table->foreign('operador_id')->references('id')->on('usuarios');

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign(['rider_id']);
            $table->dropForeign(['operador_id']);
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['cliente_id']);
        });

        Schema::dropIfExists('ordenes');
    }
};
