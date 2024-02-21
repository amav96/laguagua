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

        Schema::table('paradas_comprobantes', function (Blueprint $table) {
            $table->dropForeign(['parada_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('paradas_comprobantes');
    }
};
