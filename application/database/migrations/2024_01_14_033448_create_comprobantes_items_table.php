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
        Schema::create('comprobantes_items', function (Blueprint $table) {
            $table->id();

            $table->string("path")->nullable();

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('usuarios');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('comprobantes_items');
    }
};
