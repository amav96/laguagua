<?php

use App\Models\Item;
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
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('rider_id')->after("creado_por")->nullable();
            $table->foreign('rider_id')->references('id')->on('usuarios');

            $table->unsignedBigInteger('creado_por')->nullable()->change();
        });

        DB::table('items')
            ->whereNotNull('creado_por')
            ->update([
                'rider_id' => DB::raw('creado_por')
            ]);
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
