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
        });

        RecorridoEstado::insert([
            [
                "nombre" => "preparado",
            ],
            [
                "nombre" => "iniciado",
            ],
            [
                "nombre" => "finalizado",
            ],
            [
                "nombre" => "cancelado",
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
