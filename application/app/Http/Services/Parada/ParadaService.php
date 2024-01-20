<?php

namespace App\Http\Services\Parada;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\EstadoParada;
use App\Models\Parada;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ParadaService {

    public function findAll(array $parametros, int $userId,  array $permisos = []) {

        $query = Parada::query();
        $query = $query
                ->when(isset($parametros["incluye"]), function (Builder $q) use($parametros) : void {
                    $q->with(explode(",", $parametros["incluye"]));
                })
                ->when(isset($parametros["parada_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('id', $parametros["parada_id"]); 
                })
                ->when(count($permisos) === 0, function (Builder $q) use($parametros, $userId) : void {
                    $q->where('rider_id', $userId); 
                });

        if(isset($parametros["page"])){
            $query = $query->paginate();
        } else {
            $query = $query->get();
        }

        return $query;

    }

    public function create(array $request) : Parada {

        beginTransaction();
         try {

            $parada = new Parada();
            $parada->recorrido_id           = $request["recorrido_id"];
            $parada->lat                    = $request["lat"];
            $parada->lng                    = $request["lng"];
            $parada->direccion_formateada   = $request["direccion_formateada"];
            $parada->codigo_postal          = $request["codigo_postal"];
            $parada->localidad              = $request["localidad"];
            $parada->provincia              = $request["provincia"];
            $parada->rider_id               = $request["rider_id"];
            $parada->estado_parada_id       = EstadoParada::PREPARADO;

            $parada->save();
            $parada->load([
                "estadoParada"
            ]);

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
                "estadoParada"
            ]);

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::PARADA_ACTUALIZAR_ERROR_MESSAGE, AppErrors::PARADA_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        return $parada;

    }
    

    public function perteneceUsuario(int $riderId, int $paradaId){
        return Parada::where('rider_id', $riderId)->where('id', $paradaId)->exists();
    }

}