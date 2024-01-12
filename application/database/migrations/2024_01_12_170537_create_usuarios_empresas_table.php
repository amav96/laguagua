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
        Schema::create('usuarios_empresas', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('usuarios');

            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresas');

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


        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
        });

        Schema::dropIfExists('usuarios_empresas');
    }
};
