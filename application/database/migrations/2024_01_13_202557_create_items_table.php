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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('tipo_item_id');
            $table->foreign('tipo_item_id')->references('id')->on('tipos_items');

            $table->string('track_id')->nullable();

            $table->unsignedBigInteger('proveedor_item_id');
            $table->foreign('proveedor_item_id')->references('id')->on('proveedores_items');

            $table->unsignedBigInteger('estado_item_id');
            $table->foreign('estado_item_id')->references('id')->on('estados_items');

            $table->string('destinatario')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['tipo_item_id']);
            $table->dropForeign(['proveedor_item_id']);
            $table->dropForeign(['estado_item_id']);
        });

        Schema::dropIfExists('items');
    }
};
