<?php

use App\Models\ProveedorItem;
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
        Schema::create('proveedores_items', function (Blueprint $table) {
            $table->id();
            
            $table->string("nombre");

            $table->integer("escanear")->default(0);

            $table->timestamps();
        });

        ProveedorItem::insert([
            [
                "nombre" => "MERCADO LIBRE"
            ],
            [
                "nombre" => "INDEPENDIENTE"
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores_items');
    }
};
