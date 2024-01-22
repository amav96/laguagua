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
        });

        ItemEstado::insert([
            [
                "nombre"    => "Preparado",
                "codigo"    => "preparado"
            ],
            [
                "nombre"    => "En camino",
                "codigo"    => "en-camino"
            ],
            [
                "nombre"    => "Entregado",
                "codigo"    => "entregado"
            ],
            [
                "nombre"    => "Retirado",
                "codigo"    => "retirado"
            ],
            [
                "nombre"    => "Cancelado",
                "codigo"    => "cancelado"
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
