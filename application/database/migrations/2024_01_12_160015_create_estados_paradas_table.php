<?php

use App\Models\EstadoParada;
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
        Schema::create('estados_paradas', function (Blueprint $table) {
            $table->id();

            $table->string("nombre");

            $table->string("codigo");

        });

        EstadoParada::insert([
            [
                "nombre" => "Preparado",
                "codigo" => "preparado",
            ],
            [
                "nombre" => "En camino",
                "codigo" => "en-camino",
            ],
            [
                "nombre" => "Visitado",
                "codigo" => "visitado",
            ],
            [
                "nombre" => "Cancelado",
                "codigo" => "cancelado",
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_paradas');
    }
};
