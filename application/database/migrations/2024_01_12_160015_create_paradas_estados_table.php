<?php

use App\Models\ParadaEstado;
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
        Schema::create('paradas_estados', function (Blueprint $table) {
            $table->id();

            $table->string("nombre");

            $table->string("codigo");

            $table->string("color");

            $table->string("tipo");

        });

        ParadaEstado::insert([
            [
                "nombre" => "Preparado",
                "codigo" => "preparado",
                "color"  => "",
                "tipo"   => "positivo",
            ],
            [
                "nombre" => "En camino",
                "codigo" => "en-camino",
                "color"  => "#448aff",
                "tipo"   => "positivo",
            ],
            [
                "nombre" => "Visitado",
                "codigo" => "visitado",
                "color"  => "#00c853",
                "tipo"   => "positivo",
            ],
            [
                "nombre" => "Cancelado",
                "codigo" => "cancelado",
                "color"  => "#F31B1B",
                "tipo"   => "negativo",
            ],
            [
                "nombre" => "No responde",
                "codigo" => "no-responde",
                "color"  => "#F31B1B",
                "tipo"   => "negativo",
            ],
            [
                "nombre" => "Direccion incorrecta",
                "codigo" => "direccion-incorrecta",
                "color"  => "#F31B1B",
                "tipo"   => "negativo",
            ],
            [
                "nombre" => "Faltan datos",
                "codigo" => "faltan-datos",
                "color"  => "#F31B1B",
                "tipo"   => "negativo",
            ],
            [
                "nombre" => "Rechazado",
                "codigo" => "rechazado",
                "color"  => "#F31B1B",
                "tipo"   => "negativo",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paradas_estados');
    }
};
