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
        Schema::create('paradas_items', function (Blueprint $table) {

            $table->unsignedBigInteger('parada_id');
            $table->foreign('parada_id')->references('id')->on('paradas');

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');

            $table->unique(['parada_id', 'item_id']);

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('paradas_items', function (Blueprint $table) {
            $table->dropForeign(['parada_id']);
            $table->dropForeign(['item_id']);
        });


        Schema::dropIfExists('paradas_items');
    }
};
