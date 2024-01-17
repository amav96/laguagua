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
        Schema::create('ordenes_items', function (Blueprint $table) {

            $table->unsignedBigInteger('orden_id')->nullable();
            $table->foreign('orden_id')->references('id')->on('ordenes');

            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('item_id')->references('id')->on('items');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('ordenes_items', function (Blueprint $table) {
            $table->dropForeign(['orden_id']);
            $table->dropForeign(['item_id']);
        });

        Schema::dropIfExists('ordenes_items');
    }
};
