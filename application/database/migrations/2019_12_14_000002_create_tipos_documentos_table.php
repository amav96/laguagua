<?php

use App\Models\TipoDocumento;
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
        Schema::create('tipos_documentos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
        });

        TipoDocumento::insert([
            [
                "nombre" => "DNI",
            ],
            [
                "nombre" => "CUIT",
            ],
            [
                "nombre" => "RUT",
            ],
            [
                "nombre" => "CI",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_documentos');
    }
};
