<?php

use App\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $paradasItems = DB::table("paradas_items")->get();

        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('parada_id')->nullable()->after("id");
            $table->foreign('parada_id')->references('id')->on('paradas');
        });

        foreach($paradasItems as $paradaItem) {
            Item::find($paradaItem->item_id)->update([
                "parada_id" => $paradaItem->parada_id
            ]);
        }

        Schema::dropIfExists('paradas_items');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paradas_items', function (Blueprint $table) {
            $table->dropForeign(['parada_id']);
            $table->dropColumn(['parada_id']);
        });
    }
};
