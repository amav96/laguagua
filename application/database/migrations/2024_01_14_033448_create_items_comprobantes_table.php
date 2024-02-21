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
        Schema::create('items_comprobantes', function (Blueprint $table) {
            $table->id();

            $table->string("path")->nullable();

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('usuarios');

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('items_comprobantes', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('items_comprobantes');
    }
};
