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
        Schema::create('recorridos', function (Blueprint $table) {
            
            $table->id();
            
            $table->unsignedBigInteger('rider_id');
            $table->foreign('rider_id')->references('id')->on('usuarios');

            $table->unsignedBigInteger('creador_por');
            $table->foreign('creador_por')->references('id')->on('usuarios');

            $table->unsignedBigInteger('estado_recorrido_id');
            $table->foreign('estado_recorrido_id')->references('id')->on('estados_recorridos');

            $table->timestamp("inicio");
            
            $table->timestamp("finalizado")->nullable();

            $table->double("origen_lat")->nullable();

            $table->double("origen_lng")->nullable();

            $table->double("destino_lat")->nullable();

            $table->double("destino_lng")->nullable();
            
            $table->string("origen_formateado")->nullable();

            $table->string("destino_formateado")->nullable();

            $table->integer("optimizado")->default(0);

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

        Schema::table('recorridos', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
        });


        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['rider_id']);
        });

        Schema::table('estados_recorridos', function (Blueprint $table) {
            $table->dropForeign(['estado_recorrido_id']);
        });

        Schema::dropIfExists('recorridos');
    }
};
