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
        });

        ItemEstado::insert([
            [
                "nombre"    => "Preparado",
                "codigo"    => "preparado",
                "color"     => "#FFB133"
            ],
            [
                "nombre"    => "En camino",
                "codigo"    => "en-camino",
                "color"     => "#59DF2B"
            ],
            [
                "nombre"    => "Entregado",
                "codigo"    => "entregado",
                "color"     => "#53F31B"
            ],
            [
                "nombre"    => "Retirado",
                "codigo"    => "retirado",
                "color"     => "#53F31B"
            ],
            [
                "nombre"    => "Cancelado",
                "codigo"    => "cancelado",
                "color"     => "#F31B1B"
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
