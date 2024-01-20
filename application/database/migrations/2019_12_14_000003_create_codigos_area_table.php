<?php

use App\Models\CodigoArea;
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
        Schema::create('codigos_area', function (Blueprint $table) {
            $table->id();
            $table->string("codigo");
            $table->string("iso")->nullable();
            $table->string("bandera")->nullable();
        });

        CodigoArea::insert([
            [
                "codigo" => "+54",
                "iso"    => "AR", 
                "bandera"=> "AR",
            ],
            [
                "codigo" => "+549",
                "iso"    => "BOL", 
                "bandera"=> "BO",
            ],
            [
                "codigo" => "+55",
                "iso"    => "BRA", 
                "bandera"=> "BR",
            ],
            [
                "codigo" => "+57",
                "iso"    => "COL",
                "bandera"=> "CO",
            ],
            [
                "codigo" => "+593",
                "iso"    => "ECU", 
                "bandera"=> "EC",
            ],
            [
                "codigo" => "+595",
                "iso"    => "PRY", 
                "bandera"=> "PY",
            ],
            [
                "codigo" => "+51",
                "iso"    => "PER", 
                "bandera"=> "PE",
            ],
            [
                "codigo" => "+598",
                "iso"    => "URY", 
                "bandera"=> "UY",
            ],
            [
                "codigo" => "+54",
                "iso"    => "VEN", 
                "bandera"=> "VE",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigos_area');
    }
};
