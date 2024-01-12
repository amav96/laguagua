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

            $table->string("lat");

            $table->string("lng");

            $table->string("direccion_formateada");

            $table->string("localidad");

            $table->string("provincia");

            $table->unsignedBigInteger('estado_parada_id')->nullable();
            $table->foreign('estado_parada_id')->references('id')->on('estados_paradas');

            $table->unsignedBigInteger('tipo_parada_id')->nullable();
            $table->foreign('tipo_parada_id')->references('id')->on('tipos_paradas');

            $table->unsignedBigInteger('proveedor_item_id')->nullable();
            $table->foreign('proveedor_item_id')->references('id')->on('proveedores_items');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('recorridos', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
        });
        
        Schema::table('estados_paradas', function (Blueprint $table) {
            $table->dropForeign(['estado_parada_id']);
        });

        Schema::table('tipos_paradas', function (Blueprint $table) {
            $table->dropForeign(['tipo_parada_id']);
        });

        Schema::table('proveedores_items', function (Blueprint $table) {
            $table->dropForeign(['proveedor_item_id']);
        });

        Schema::dropIfExists('paradas');
    }
};
