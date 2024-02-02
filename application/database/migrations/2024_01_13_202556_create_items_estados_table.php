<?php

use App\Models\ItemEstado;
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
        Schema::create('items_estados', function (Blueprint $table) {
            $table->id();

            $table->string("nombre");

            $table->string("codigo");

            $table->string("color");

            $table->string("tipo");
        });

        ItemEstado::insert([
            [
                "nombre"    => "Preparado",
                "codigo"    => "preparado",
                "color"     => "#FFB133",
                "tipo"      => "positivo"
            ],
            [
                "nombre"    => "En camino",
                "codigo"    => "en-camino",
                "color"     => "#59DF2B",
                "tipo"      => "positivo"
            ],
            [
                "nombre"    => "Entregado",
                "codigo"    => "entregado",
                "color"     => "#00c853",
                "tipo"      => "positivo"
            ],
            [
                "nombre"    => "Retirado",
                "codigo"    => "retirado",
                "color"     => "#53F31B",
                "tipo"      => "positivo"
            ],
            [
                "nombre"    => "Cancelado",
                "codigo"    => "cancelado",
                "color"     => "#F31B1B",
                "tipo"      => "negativo"
            ],
            [
                "nombre"    => "No responde",
                "codigo"    => "no-responde",
                "color"     => "",
                "tipo"      => "negativo"
            ],
            [
                "nombre"    => "Dirección incorrecta",
                "codigo"    => "direccion-incorrecta",
                "color"     => "",
                "tipo"      => "negativo"
            ],
            [
                "nombre"    => "Faltan datos",
                "codigo"    => "faltan-datos",
                "color"     => "",
                "tipo"      => "negativo"
            ],
            [
                "nombre"    => "Rechazado",
                "codigo"    => "rechazado",
                "color"     => "",
                "tipo"      => "negativo"
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_estados');
    }
};
