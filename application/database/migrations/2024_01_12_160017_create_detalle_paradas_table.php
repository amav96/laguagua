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
        Schema::create('detalle_paradas', function (Blueprint $table) {
            $table->id();

            $table->string("destinatario")->nullable();

            $table->unsignedBigInteger('parada_id')->nullable();
            $table->foreign('parada_id')->references('id')->on('paradas');

            $table->unsignedBigInteger('tipo_documento_id')->nullable();
            $table->foreign('tipo_documento_id')->references('id')->on('tipos_documentos');

            $table->string("numero_documento")->nullable();

            $table->unsignedBigInteger('codigo_area_id')->nullable();
            $table->foreign('codigo_area_id')->references('id')->on('codigos_area');

            $table->string("numero_celular")->nullable();

            $table->string("numero_fijo")->nullable();

            $table->string("track_number")->nullable();
            
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

        Schema::table('tipos_documentos', function (Blueprint $table) {
            $table->dropForeign(['tipo_documento_id']);
        });

        Schema::table('codigos_area', function (Blueprint $table) {
            $table->dropForeign(['codigo_area_id']);
        });

        Schema::dropIfExists('detalle_paradas');
    }
};
