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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->string('nombre')->nullable();

            $table->unsignedBigInteger('tipo_documento_id')->nullable();
            $table->foreign('tipo_documento_id')->references('id')->on('tipos_documentos');

            $table->string('numero_documento')->nullable();

            $table->unsignedBigInteger('codigo_area_id')->nullable();
            $table->foreign('codigo_area_id')->references('id')->on('codigos_area');

            $table->string('numero_celular')->nullable();

            $table->string('numero_fijo')->nullable();

            $table->longText('observaciones')->nullable();

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedBigInteger('creado_por');
            $table->foreign('creado_por')->references('id')->on('usuarios');

            $table->timestamps();

            $table->index(['tipo_documento_id']);

            $table->index(['numero_documento']);
            
            $table->index(['nombre']);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('tipos_documentos', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
        });

        Schema::table('codigos_area', function (Blueprint $table) {
            $table->dropForeign(['codigo_area_id']);
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['creado_por']);
        });

        Schema::dropIfExists('clientes');
    }
};
