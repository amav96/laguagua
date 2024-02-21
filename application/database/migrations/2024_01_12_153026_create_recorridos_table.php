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

            $table->unsignedBigInteger('creado_por');
            $table->foreign('creado_por')->references('id')->on('usuarios');

            $table->unsignedBigInteger('recorrido_estado_id');
            $table->foreign('recorrido_estado_id')->references('id')->on('recorridos_estados');

            $table->timestamp("inicio");
            
            $table->timestamp("finalizado")->nullable();

            $table->double("origen_lat")->nullable();

            $table->double("origen_lng")->nullable();

            $table->double("destino_lat")->nullable();

            $table->double("destino_lng")->nullable();
            
            $table->string("origen_formateado")->nullable();

            $table->string("destino_formateado")->nullable();

            $table->double("origen_actual_lat")->nullable();

            $table->double("origen_actual_lng")->nullable();

            $table->string("origen_actual_formateado")->nullable();

            $table->integer("origen_auto")->default(0);
            
            $table->integer("optimizado")->default(0);

            $table->string("distancia")->nullable();

            $table->string("duracion")->nullable();

            $table->longText("polyline")->nullable();
            
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
            $table->dropForeign(['rider_id']);
            $table->dropForeign(['recorrido_estado_id']);
        });

        Schema::dropIfExists('recorridos');
    }
};
