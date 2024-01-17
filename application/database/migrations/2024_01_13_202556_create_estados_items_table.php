<?php

use App\Models\EstadoItem;
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
        Schema::create('estados_items', function (Blueprint $table) {
            $table->id();

            $table->string("nombre");

            $table->string("codigo");
        });

        EstadoItem::insert([
            [
                "nombre"    => "En espera",
                "codigo"    => "en-espera"
            ],
            [
                "nombre"    => "Preparado",
                "codigo"    => "preparado"
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
        Schema::dropIfExists('estados_items');
    }
};
