<?php

namespace App\Http\Services\Parada;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\ParadaEstado;
use App\Models\Parada;
use App\Models\Recorrido;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ParadaService {

    public function findAll(array $parametros) {

        $query = Parada::query();
        $query = $query
                ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                    $q->with($parametros["incluir"]);
                })
                ->when(isset($parametros["parada_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('id', $parametros["parada_id"]); 
                })
                ->when(isset($parametros["rider_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('rider_id', $parametros["rider_id"]); 
                });
                
        return $this->transform(isset($parametros["page"]) ?  $query->paginate() : $query->get(), $parametros["time_zone"]);
    }

    private function transform($items, string $timeZone){
        if ($items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $items->getCollection()->transform(function($parada) use($timeZone){
                $parada->items = $parada->items->map(function($item) use($timeZone) {
                    if(!$item->gestionado){
                        return $item;
                    }
                    $item->gestionado_formateado = Carbon::parse($item->gestionado)->setTimezone($timeZone)->format('d-m-y H:i:s');
                    return $item;
                });
                return $parada;
            });
        } else {
            $items->transform(function($parada) use($timeZone){
                $parada->items = $parada->items->map(function($item) use($timeZone) {
                    if(!$item->gestionado){
                        return $item;
                    }
                    $item->gestionado_formateado = Carbon::parse($item->gestionado)->setTimezone($timeZone)->format('d-m-y H:i:s');
                    return $item;
                });
                return $parada;
            });
        }
        return $items;
    }

    public function create(array $request) : Parada {

        beginTransaction();
         try {

            $parada = new Parada();
            $parada->recorrido_id           = $request["recorrido_id"];
            $parada->lat                    = $request["lat"];
            $parada->lng                    = $request["lng"];
            $parada->direccion_formateada   = $request["direccion_formateada"];
            $parada->codigo_postal          = $request["codigo_postal"] ?? '';
            $parada->localidad              = $request["localidad"] ?? '';
            $parada->provincia              = $request["provincia"] ?? '';
            $parada->rider_id               = $request["rider_id"];
            $parada->parada_estado_id       = ParadaEstado::PREPARADO;
            $parada->tipo_domicilio         = $request["tipo_domicilio"] ?? null;

            $parada->save();
            $parada->load([
                "paradaEstado"
            ]);

            Recorrido::where("id", $parada->recorrido_id)->update(["optimizado" => 0]);

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::PARADA_CREAR_ERROR_MESSAGE, AppErrors::PARADA_CREAR_ERROR_CODE);
        }

        commit();
        return $parada;

    }

    public function update(array $request, Parada $parada) : Parada {

        beginTransaction();
        try {

            $parada->lat                    = $request["lat"];
            $parada->lng                    = $request["lng"];
            $parada->direccion_formateada   = $request["direccion_formateada"];
            $parada->codigo_postal          = $request["codigo_postal"];
            $parada->localidad              = $request["localidad"];
            $parada->provincia              = $request["provincia"];
           

            $parada->save();
            $parada->load([
                "paradaEstado"
            ]);

            Recorrido::where("id", $parada->recorrido_id)->update(["optimizado" => 0]);

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::PARADA_ACTUALIZAR_ERROR_MESSAGE, AppErrors::PARADA_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        return $parada;

    }

    public function updateEstado(array $request, Parada $parada){

        $parada->parada_estado_id   = $request["parada_estado_id"];

        $paradaEstadoService = new ParadaEstadoService();
        
        if($paradaEstadoService->paradaVisitada($request["parada_estado_id"])){
            $parada->realizado_en = now();
        }

        $parada->save();

        $parada->load([
            "paradaEstado"
        ]);

        return $parada;
    }
    
    public function obtenerEstadoParadaConEstadoItem( string $codigo){

        $paradaEstadoId = null;
        switch($codigo){
            case "preparado";
            $paradaEstadoId = ParadaEstado::PREPARADO;
            break;
            case "en-camino";
            $paradaEstadoId = ParadaEstado::EN_CAMINO;
            break;
            case "entregado";
            $paradaEstadoId = ParadaEstado::VISITADO;
            break;
            case "retirado";
            $paradaEstadoId = ParadaEstado::VISITADO;
            break;
            case "cancelado";
            $paradaEstadoId = ParadaEstado::CANCELADO;
            break;
            case "no-responde";
            $paradaEstadoId = ParadaEstado::NO_RESPONDE;
            break;
            case "direccion-incorrecta";
            $paradaEstadoId = ParadaEstado::DIRECCION_INCORRECTA;
            break;
            case "faltan-datos";
            $paradaEstadoId = ParadaEstado::FALTAN_DATOS;
            break;
            case "rechazado";
            $paradaEstadoId = ParadaEstado::RECHAZADO;
            break;
        }

        return $paradaEstadoId;

    }

    public function perteneceUsuario(int $riderId, int $paradaId){
        return Parada::where('rider_id', $riderId)->where('id', $paradaId)->exists();
    }

}