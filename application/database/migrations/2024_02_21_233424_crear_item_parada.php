<?php

use App\Models\Item;
use App\Models\ItemEstado;
use App\Models\ItemProveedor;
use App\Models\ItemTipo;
use App\Models\Parada;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $items = Item::whereNotNull("parada_id")->get();
        if($items->isNotEmpty()){
            $paradasSinItems = Parada::whereNotIn("id", $items->pluck("parada_id"))->get();
            foreach($paradasSinItems as $parada){
                Item::create([
                    "item_tipo_id"          => ItemTipo::ENTREGA,
                    "item_proveedor_id"     => ItemProveedor::MERCADO_LIBRE,
                    "empresa_id"            => 1,
                    "item_estado_id"        => ItemEstado::PREPARADO,
                    "track_id"              => null,
                    "cliente_id"            => null,
                    "destinatario"          => null,
                    "parada_id"             => $parada->id,
                    "creado_por"            => $parada->rider_id
                ]);
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

