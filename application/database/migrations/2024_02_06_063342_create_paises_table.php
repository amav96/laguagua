<?php

use App\Models\Pais;
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
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("iso");
            
        });

        Pais::insert([
            [
                "nombre" => "Argentina",
                "iso"    => "AR", 
            ],
            [
                "nombre" => "Bolivia",
                "iso"    => "BOL", 
            ],
            [
                "nombre" => "Brasil",
                "iso"    => "BRA", 
            ],
            [
                "nombre" => "Colombia",
                "iso"    => "COL",
            ],
            [
                "nombre" => "Ecuador",
                "iso"    => "ECU", 
            ],
            [
                "nombre" => "Paraguay",
                "iso"    => "PRY", 
            ],
            [
                "nombre" => "Peru",
                "iso"    => "PER", 
            ],
            [
                "nombre" => "Uruguay",
                "iso"    => "URY", 
            ],
            [
                "nombre" => "Venezuela",
                "iso"    => "VEN", 
            ],
            [
                "nombre" => "España",
                "iso"    => "ES", 
            ],
        ]);

        Schema::table('usuarios', function (Blueprint $table) {
            $table->integer('pais_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
