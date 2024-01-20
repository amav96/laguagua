<?php

use App\Models\TipoItem;
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
        Schema::create('tipos_items', function (Blueprint $table) {
            $table->id();

            $table->string("nombre");

            $table->string("codigo");
        });

        TipoItem::insert([
            [
                "nombre"    => "Entrega",
                "codigo"    => "entrega"
            ],
            [
                "nombre"    => "Retiro",
                "codigo"    => "retiro"
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_items');
    }
};
