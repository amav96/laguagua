<?php

use App\Models\RecorridoEstado;
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
        Schema::create('recorridos_estados', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');

            $table->string('codigo');

            $table->string('color')->nullable();
        });

        RecorridoEstado::insert([
            [
                "nombre" => "Preparado",
                "codigo" => "preparado",
                "color"  => "#FFB133",
            ],
            [
                "nombre" => "Iniciado",
                "codigo" => "iniciado",
                "color"  => "#448aff",
            ],
            [
                "nombre" => "Finalizado",
                "codigo" => "finalizado",
                "color"  => "#00c853",
            ],
            [
                "nombre" => "Cancelado",
                "codigo" => "cancelado",
                "color"  => "#F31B1B",
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorridos_estados');
    }
};
