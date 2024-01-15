<?php

namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\EstadoItem;
use App\Models\Item;
use App\Models\ParadaItem;

class ItemService {

    public function create(array $request, int|null $paradaId = null) : Item{


        $estadoItemId = isset($request["estado_item_id"]) 
        ? $request["estado_item_id"] 
        : ((isset($paradaId)) 
            ? EstadoItem::PREPARADO 
            : EstadoItem::EN_ESPERA);

        $this->validarItem($request);

        beginTransaction();
        try {

           
            $item = Item::create([
                "tipo_item_id"          => $request["tipo_item_id"],
                "proveedor_item_id"     => $request["proveedor_item_id"],
                "estado_item_id"        => $estadoItemId,
                "track_id"              => $request["track_id"] ?? null,
                "cliente_id"            => $request["cliente_id"] ?? null,
                "destinatario"          => $request["destinatario"] ?? null,
            ]);

            if(isset($paradaId)){
                ParadaItem::create([
                    "item_id"   => $item->id,
                    "parada_id" => $paradaId
                ]);
            }

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::ITEM_CREAR_ERROR_MESSAGE, AppErrors::ITEM_CREAR_ERROR_CODE);
        }

        commit();

        return $item->load([
            "cliente",
            "tipoItem",
            "proveedorItem",
            "estadoItem"
        ]);

    }

    private function validarItem(array $request){
        if(isset($request["track_id"]) && Item::where("proveedor_item_id", $request["proveedor_item_id"])
                ->where("cliente_id", $request["cliente_id"])
                ->where("track_id", $request["track_id"])
                ->exists()){
            throw new BussinessException(AppErrors::ITEM_CREAR_DUPLICADO_ERROR_MESSAGE, AppErrors::ITEM_CREAR_DUPLICADO_ERROR_CODE);
        }
    }
}