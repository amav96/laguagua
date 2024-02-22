<?php

use App\Models\Item;
use App\Models\ItemEstado;
use App\Models\Parada;
use App\Models\ParadaEstado;
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
        $paradas = Parada::where("parada_estado_id", ParadaEstado::VISITADO);
        $items = Item::with("parada")->whereNotNull("parada_id")
                    ->whereNull("gestionado")
                    ->where("item_estado_id", ItemEstado::PREPARADO)
                    ->whereIn("parada_id",$paradas->pluck("id"))
                    ->get();

        if($items->isNotEmpty()){
            foreach($items as $item){
                $item->item_estado_id = ItemEstado::ENTREGADO;
                $item->gestionado = $item->parada->realizado_en;
                $item->created_at = $item->parada->realizado_en;
                $item->updated_at = $item->parada->realizado_en;
                $item->save();
            }
        }
       

    
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
