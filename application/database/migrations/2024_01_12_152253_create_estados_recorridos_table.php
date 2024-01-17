<?php

use App\Models\EstadoRecorrido;
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
        Schema::create('estados_recorridos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        EstadoRecorrido::insert([
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
        Schema::dropIfExists('estados_recorridos');
    }
};
