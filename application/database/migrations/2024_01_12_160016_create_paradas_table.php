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
        Schema::create('paradas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recorrido_id')->nullable();
            $table->foreign('recorrido_id')->references('id')->on('recorridos');

            $table->double("lat");

            $table->double("lng");

            $table->string("direccion_formateada");

            $table->string("codigo_postal");

            $table->string("localidad");

            $table->string("provincia");

            $table->string("tipo_domicilio")->nullable();;

            $table->unsignedBigInteger('parada_estado_id')->nullable();
            $table->foreign('parada_estado_id')->references('id')->on('paradas_estados');

            $table->dateTime("realizado_en")->nullable();

            $table->unsignedBigInteger('rider_id')->nullable();
            $table->foreign('rider_id')->references('id')->on('usuarios');

            $table->integer("orden")->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('paradas', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
            $table->dropForeign(['parada_estado_id']);
        });
        
        Schema::dropIfExists('paradas');
    }
};
