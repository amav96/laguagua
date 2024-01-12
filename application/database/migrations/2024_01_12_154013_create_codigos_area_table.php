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
        });

        CodigoArea::insert([
            [
                "codigo" => "+54",
                "iso"    => "AR", 
            ],
            [
                "codigo" => "+549",
                "iso"    => "BOL", 
            ],
            [
                "codigo" => "+55",
                "iso"    => "BRA", 
            ],
            [
                "codigo" => "+57",
                "iso"    => "COL", 
            ],
            [
                "codigo" => "+593",
                "iso"    => "ECU", 
            ],
            [
                "codigo" => "+595",
                "iso"    => "PRY", 
            ],
            [
                "codigo" => "+51",
                "iso"    => "PER", 
            ],
            [
                "codigo" => "+598",
                "iso"    => "URY", 
            ],
            [
                "codigo" => "+54",
                "iso"    => "VEN", 
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
