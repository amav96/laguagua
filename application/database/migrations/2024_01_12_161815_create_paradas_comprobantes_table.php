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
        Schema::create('paradas_comprobantes', function (Blueprint $table) {
            $table->id();

            $table->string("path");

            $table->unsignedBigInteger('parada_id')->nullable();
            $table->foreign('parada_id')->references('id')->on('paradas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('paradas', function (Blueprint $table) {
            $table->dropForeign(['parada_id']);
        });

        Schema::dropIfExists('paradas_comprobantes');
    }
};
