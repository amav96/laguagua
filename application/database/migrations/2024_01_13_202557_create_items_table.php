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

            $table->unsignedBigInteger('item_tipo_id');
            $table->foreign('item_tipo_id')->references('id')->on('items_tipos');

            $table->string('track_id')->nullable();

            $table->unsignedBigInteger('item_proveedor_id');
            $table->foreign('item_proveedor_id')->references('id')->on('items_proveedores');

            $table->unsignedBigInteger('item_estado_id');
            $table->foreign('item_estado_id')->references('id')->on('items_estados');

            $table->string('destinatario')->nullable();

            $table->timestamp('entregar')->nullable();

            $table->timestamp('entregado')->nullable();

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedBigInteger('creado_por');
            $table->foreign('creado_por')->references('id')->on('usuarios');

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
            $table->dropForeign(['item_tipo_id']);
            $table->dropForeign(['item_proveedor_id']);
            $table->dropForeign(['item_estado_id']);
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['creado_por']);
        });

        Schema::dropIfExists('items');
    }
};
