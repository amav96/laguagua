<?php

namespace App\Http\Services\Recorrido;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Recorrido;
use App\Models\RecorridoEstado;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RecorridoService {

    public function findAll(array $parametros, array $permisos = [], int $usuarioAutenticadoId) {

        $query = Recorrido::query();

    
        $query = $query
                ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                    $q->with($parametros["incluir"]);
                })
                ->when($this->incluyeParada($parametros), function (Builder $q) : void {
                    $q->with(['paradas' => function ($query) {
                        $query->orderBy('orden', 'asc'); 
                    }]);
                })
                ->when(isset($parametros["recorrido_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('id', $parametros["recorrido_id"]); 
                })
                ->when(isset($parametros["inicio"]), function (Builder $q) use($parametros) : void {
                    $q->whereDate('inicio', '>=', $parametros["inicio"] . ' 00:00:00')
                      ->where('inicio', '<=', $parametros["inicio"] . ' 23:59:59');
                })
                ->when(isset($parametros["rider_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('rider_id', $parametros["rider_id"]); 
                })
                ->when(count($permisos) === 0, function (Builder $q) use($parametros, $usuarioAutenticadoId) : void {
                    $q->where('rider_id', $usuarioAutenticadoId); 
                })
                ->orderBy('inicio', 'DESC');

        if(isset($parametros["page"])){
            $query = $query->paginate();
            // TODO: configurar timezone en usuario configuracones
            $timeZoneFront = "America/Argentina/Buenos_Aires";
            $items = $query->getCollection()->map(function ($item) use ($timeZoneFront) {
                $item->inicio = Carbon::parse($item->inicio)->setTimezone($timeZoneFront)->format('d-m-y H:i:s');
                return $item;
            });
            
            // Actualizar la colección de la paginación con los items modificados
            $query->setCollection($items);
        } else {
            $query = $query->get();
        }

        return $query;

    }

    private function incluyeParada($parametros)
    {
        if (!isset($parametros["incluir"]) || !is_array($parametros["incluir"])) {
            return false; // No se incluyen paradas si no se proporciona un array de inclusión
        }

        foreach ($parametros["incluir"] as $incluir) {
            if (strpos($incluir, "paradas") !== false) {
                return true; // Se incluyen paradas si al menos un elemento del array contiene la palabra "paradas"
            }
        }

        return false; // No se incluyen paradas si ninguno de los elementos del array contiene la palabra "paradas"
    }

    public function create(array $request, int $creadoPor){

        try {
  
            // TODO: configurar timezone en usuario configuracones
            $timeZoneFront = "America/Argentina/Buenos_Aires";
            $inicio  = Carbon::parse($request["inicio"], $timeZoneFront)
            ->setTimezone(config('app.timezone'));
  
            $recorrido = Recorrido::create([
                "rider_id"              => $request["rider_id"],
                // "empresa_id"            => $request["empresa_id"],
                "recorrido_estado_id"   => RecorridoEstado::PREPARADO,
                "inicio"                => $inicio,
                "creado_por"            => $creadoPor
            ]);

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_CREAR_ERROR_MESSAGE, AppErrors::RECORRIDO_CREAR_ERROR_CODE);
        }

        return $recorrido;
    }

    public function updateOrigen(array $request, int $recorridoId) : Recorrido {

        try {
            
            $recorrido = Recorrido::find($recorridoId);
            $recorrido->origen_lat = $request["origen_lat"];
            $recorrido->origen_lng = $request["origen_lng"];
            $recorrido->origen_formateado = $request["origen_formateado"];
            $recorrido->origen_actual_lat = $request["origen_lat"];
            $recorrido->origen_actual_lng = $request["origen_lng"];
            $recorrido->origen_actual_formateado = $request["origen_formateado"];
            $recorrido->origen_auto = $request["origen_auto"];
            $recorrido->optimizado = 0;
            $recorrido->save();


        } catch (\Throwable $th) {
           
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

 
        return $recorrido;
    }

    public function updateOrigenActual(array $request, int $recorridoId) : Recorrido {

        try {

            $recorrido = Recorrido::find($recorridoId);
            $recorrido->origen_actual_lat = $request["origen_actual_lat"];
            $recorrido->origen_actual_lng = $request["origen_actual_lng"];
            $recorrido->origen_actual_formateado = $request["origen_actual_formateado"];
            $recorrido->optimizado = 0;
            $recorrido->save();

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

 
        return $recorrido;
    }

    public function updateDestino(array $request, int $recorridoId) : Recorrido {

        try {

            $recorrido = Recorrido::find($recorridoId);
            $recorrido->destino_lat = $request["destino_lat"];
            $recorrido->destino_lng = $request["destino_lng"];
            $recorrido->destino_formateado = $request["destino_formateado"];
            $recorrido->optimizado = 0;
            $recorrido->save();

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

        return $recorrido;
    }

    public function removeOrigen(int $recorridoId) : Recorrido{

        try {

            $recorrido = Recorrido::find($recorridoId);
            $recorrido->origen_lat = null;
            $recorrido->origen_lng = null;
            $recorrido->origen_formateado = null;
            $recorrido->optimizado = 0;
            $recorrido->save();

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

        return $recorrido;

    }

    public function removeDestino(int $recorridoId) : Recorrido{

        try {

            $recorrido = Recorrido::find($recorridoId);
            $recorrido->destino_lat = null;
            $recorrido->destino_lng = null;
            $recorrido->destino_formateado = null;
            $recorrido->optimizado = 0;
            $recorrido->save();

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

        return $recorrido;

    }
    
    public function updateEstado(Recorrido $recorrido, array $request){

        try {

            $recorrido->recorrido_estado_id = $request["recorrido_estado_id"];
            if($request["recorrido_estado_id"] === RecorridoEstado::FINALIZADO){
                $recorrido->finalizado = now();
            }
            $recorrido->save();

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

        return $recorrido->load(['recorridoEstado']);
    }
    
    public function perteneceUsuario(int $riderId, int $recorridoId) : bool {
        return Recorrido::where("rider_id", $riderId)->where("id", $recorridoId)->exists();
    }

    public function existeRecorrido($recorrido_id){
        return Recorrido::where("id",$recorrido_id)->exists();
    }


}