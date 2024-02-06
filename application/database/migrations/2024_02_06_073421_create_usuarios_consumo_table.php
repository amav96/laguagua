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
        Schema::create('usuarios_consumos', function (Blueprint $table) {
            $table->id();
            $table->integer("cantidad_optimizar")->default(0);
            $table->double("consumo_optimizar")->default(0);
            $table->integer("cantidad_detectar")->default(0);
            $table->double("consumo_detectar")->default(0);

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

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        
        Schema::dropIfExists('usuarios_consumos');
        
    }
};
