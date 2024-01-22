<?php

use App\Models\ItemTipo;
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
        Schema::create('items_tipos', function (Blueprint $table) {
            $table->id();

            $table->string("nombre");

            $table->string("codigo");
        });

        ItemTipo::insert([
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
        Schema::dropIfExists('items_tipos');
    }
};
