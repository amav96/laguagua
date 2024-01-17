<?php

namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;

use App\Models\EstadoItem;
use App\Models\EstadoParada;
use App\Models\Item;
use App\Models\Parada;
use App\Models\ParadaItem;


class ItemService {

    public function create(array $request, int $creadoPor) : Item{


        $estadoItemId = isset($request["estado_item_id"]) 
        ? $request["estado_item_id"] 
        : ((isset($paradaId)) 
            ? EstadoItem::PREPARADO 
            : EstadoItem::EN_ESPERA);

        $this->validarItemDuplicado($request);

        beginTransaction();
        try {

           
            $item = Item::create([
                "tipo_item_id"          => $request["tipo_item_id"],
                "proveedor_item_id"     => $request["proveedor_item_id"],
                "empresa_id"            => $request["empresa_id"],
                "estado_item_id"        => $estadoItemId,
                "track_id"              => $request["track_id"] ?? null,
                "cliente_id"            => $request["cliente_id"] ?? null,
                "destinatario"          => $request["destinatario"] ?? null,
                "creador_por"           => $creadoPor
            ]);

            if(isset($request["parada_id"])){
                ParadaItem::create([
                    "item_id"   => $item->id,
                    "parada_id" => $request["parada_id"]
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

    public function update(Item $item, array $request){

        beginTransaction();
        try {

            $item->fill([
                "tipo_item_id"          => $request["tipo_item_id"],
                "proveedor_item_id"     => $request["proveedor_item_id"],
                "empresa_id"            => $request["empresa_id"],
                "estado_item_id"        => $request["estado_item_id"],
                "track_id"              => $request["track_id"] ?? $item->track_id,
                "destinatario"          => $request["destinatario"] ?? $item->destinatario,
            ]);
    
            $item->save();

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::ITEM_ACTUALIZAR_ERROR_MESSAGE, AppErrors::ITEM_ACTUALIZAR_ERROR_CODE);
        }

        commit();

        return $item->load([
            "cliente",
            "tipoItem",
            "proveedorItem",
            "estadoItem"
        ]);
    }

    public function updateEstado(Item $item, array $request){

        beginTransaction();
        try {
            $item->estado_item_id = $request["estado_item_id"];
            $item->save();

            $itemActualizado = $item->load([
                "estadoItem"
            ]);
            
            if(isset($request["parada_id"])){

                $estadoParadaId = null;
               
                switch($itemActualizado->estadoItem->codigo){
                    case "en-espera";
                    $estadoParadaId = EstadoParada::PREPARADO;
                    case "preparado";
                    $estadoParadaId = EstadoParada::PREPARADO;
                    case "en-camino";
                    $estadoParadaId = EstadoParada::EN_CAMINO;
                    case "entregado";
                    $estadoParadaId = EstadoParada::VISITADO;
                    case "retirado";
                    $estadoParadaId = EstadoParada::VISITADO;
                    case "cancelado";
                    $estadoParadaId = EstadoParada::CANCELADO;
                }
                Parada::where("id", $request["parada_id"])->update(["estado_parada_id", $estadoParadaId]);
            }

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::ITEM_ACTUALIZAR_ERROR_MESSAGE, AppErrors::ITEM_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        return $itemActualizado;
    }

    private function validarItemDuplicado(array $request){
        if(isset($request["track_id"]) &&  Item::where("proveedor_item_id", $request["proveedor_item_id"])
                ->where("empresa_id", $request["empresa_id"])
                ->where("track_id", $request["track_id"])
                ->exists()){
            throw new BussinessException(AppErrors::ITEM_CREAR_DUPLICADO_ERROR_MESSAGE, AppErrors::ITEM_CREAR_DUPLICADO_ERROR_CODE);
        }
    }

  
}