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

        });

        EstadoParada::insert([
            [
                "nombre" => "ENTREGADO",
            ],
            [
                "nombre" => "RETIRADO",
            ],
            [
                "nombre" => "CANCELADO",
            ],
            [
                "nombre" => "PREPARADO",
            ],
            [
                "nombre" => "EN_CAMINO",
            ],
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
