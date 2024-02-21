<?php

namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Querys\Item\ItemQuery;
use App\Http\Services\Parada\ParadaService;
use App\Models\ItemEstado;
use App\Models\Item;
use App\Models\Parada;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ItemService {

    public function findAll(array $parametros) {
        $query = (new ItemQuery)->findAll($parametros);
        return $this->transform(isset($parametros["page"]) ?  $query->paginate() : $query->get(), $parametros["time_zone"]);
   }

    private function transform($items, string $timeZone){
        if ($items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $items->getCollection()->transform(function($item) use($timeZone){

                $item->gestionado_transformado = $item->gestionado ? Carbon::parse($item->gestionado)->setTimezone($timeZone)->format('d-m-y H:i:s') : null;
                $item->created_at_transformado = Carbon::parse($item->created_at)->setTimezone($timeZone)->format('d-m-y H:i:s');
                return $item;
            });
        } elseif ($items instanceof \Illuminate\Support\Collection) {
            $items->transform(function($item) use($timeZone){
                $item->gestionado_transformado = $item->gestionado ? Carbon::parse($item->gestionado)->setTimezone($timeZone)->format('d-m-y H:i:s') : null;
                $item->created_at_transformado = Carbon::parse($item->created_at)->setTimezone($timeZone)->format('d-m-y H:i:s');
                return $item;
            });
        }
        return $items;
    }

    public function create(array $request, int $creadoPor) : Item{


        $itemEstadoId = isset($request["item_estado_id"]) 
        ? $request["item_estado_id"] 
        : ItemEstado::PREPARADO ;

        // $this->validarItemDuplicado($request);

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
                "parada_id"             => isset($request["parada_id"]) ? $request["parada_id"] : null,
                "creado_por"            => $creadoPor
            ]);

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
                "cliente_id"            => $request["cliente_id"] ?? null,
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
            if($request["item_estado_id"] !== ItemEstado::PREPARADO){
                $item->gestionado = Carbon::parse(now(), $request["time_zone"])
                ->setTimezone(config('app.timezone'));
            }
            $item->save();

            $itemActualizado = $item->load([
                "itemEstado"
            ]);
            
            if(isset($request["parada_id"])){
                
                $paradaService = new ParadaService();
                $data = [
                    "parada_estado_id" => $paradaService->obtenerEstadoParadaConEstadoItem($itemActualizado->itemEstado->codigo),
                ];
                $paradaService->updateEstado($data, Parada::find($request["parada_id"]));

            }

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::ITEM_ACTUALIZAR_ERROR_MESSAGE, AppErrors::ITEM_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        $parada = Parada::find($request["parada_id"]);
        $itemActualizado->parada = $parada->load('paradaEstado');
        return $itemActualizado;
    }

}