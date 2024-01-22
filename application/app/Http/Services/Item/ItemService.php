<?php

namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;

use App\Models\ItemEstado;
use App\Models\ParadaEstado;
use App\Models\Item;
use App\Models\Parada;
use App\Models\ParadaItem;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;


class ItemService {

    public function findAll(array $parametros, int $userId,  array $permisos = []) {

        $query = Item::query();
        $query = $query
                ->when(isset($parametros["incluye"]), function (Builder $q) use($parametros) : void {
                    $q->with(explode(",", $parametros["incluye"]));
                })
                ->when(isset($parametros["item_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('id', $parametros["item_id"]); 
                });

        if(isset($parametros["page"])){
            $query = $query->paginate();
        } else {
            $query = $query->get();
        }

        return $query;

    }

    public function create(array $request, int $creadoPor) : Item{


        $itemEstadoId = isset($request["item_estado_id"]) 
        ? $request["item_estado_id"] 
        : ItemEstado::PREPARADO ;

        $this->validarItemDuplicado($request);

        beginTransaction();
        try {

           
            $item = Item::create([
                "item_tipo_id"          => $request["item_tipo_id"],
                "item_proveedor_id"     => $request["item_proveedor_id"],
                "empresa_id"            => $request["empresa_id"],
                "item_estado_id"        => $itemEstadoId,
                "track_id"              => $request["track_id"] ?? null,
                "cliente_id"            => $request["cliente_id"] ?? null,
                "destinatario"          => $request["destinatario"] ?? null,
                "creado_por"           => $creadoPor
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
            "itemTipo",
            "itemProveedor",
            "itemEstado"
        ]);

    }

    public function update(Item $item, array $request){

        beginTransaction();
        try {

            $item->fill([
                "item_tipo_id"          => $request["item_tipo_id"],
                "item_proveedor_id"     => $request["item_proveedor_id"],
                "empresa_id"            => $request["empresa_id"],
                "item_estado_id"        => $request["item_estado_id"],
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
            "itemTipo",
            "itemProveedor",
            "itemEstado"
        ]);
    }

    public function updateEstado(Item $item, array $request){

        beginTransaction();
        try {
            $item->item_estado_id = $request["item_estado_id"];
            $item->save();

            $itemActualizado = $item->load([
                "itemEstado"
            ]);
            
            if(isset($request["parada_id"])){

                $paradaEstadoId = null;
               
                switch($itemActualizado->itemEstado->codigo){
                    case "en-espera";
                    $paradaEstadoId = ParadaEstado::PREPARADO;
                    case "preparado";
                    $paradaEstadoId = ParadaEstado::PREPARADO;
                    case "en-camino";
                    $paradaEstadoId = ParadaEstado::EN_CAMINO;
                    case "entregado";
                    $paradaEstadoId = ParadaEstado::VISITADO;
                    case "retirado";
                    $paradaEstadoId = ParadaEstado::VISITADO;
                    case "cancelado";
                    $paradaEstadoId = ParadaEstado::CANCELADO;
                }
                Parada::where("id", $request["parada_id"])->update(["parada_estado_id", $paradaEstadoId]);
            }

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::ITEM_ACTUALIZAR_ERROR_MESSAGE, AppErrors::ITEM_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        return $itemActualizado;
    }

    private function validarItemDuplicado(array $request){
        if(isset($request["track_id"]) &&  Item::where("item_proveedor_id", $request["item_proveedor_id"])
                ->where("empresa_id", $request["empresa_id"])
                ->where("track_id", $request["track_id"])
                ->exists()){
            throw new BussinessException(AppErrors::ITEM_CREAR_DUPLICADO_ERROR_MESSAGE, AppErrors::ITEM_CREAR_DUPLICADO_ERROR_CODE);
        }
    }

  
}