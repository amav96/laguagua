<?php


namespace App\Http\Services\Recorrido;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Recorrido;
use App\Models\RecorridoEstado;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;

class RecorridoService {

    public function findAll(array $parametros, array $permisos = [], int $usuarioAutenticadoId) {

        $query = Recorrido::query();
     
        $query = $query
                ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                    $q->with(explode(",", $parametros["incluir"]));
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
                });

        if(isset($parametros["page"])){
            $query = $query->paginate();
        } else {
            $query = $query->get();
        }

        return $query;

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
    
    public function obtenerRecorrido(array $recorrido){
        
        $apiKey = 'AIzaSyAD2gY2H88XBrGUz8sJVWYpAWkkz6n38Ds'; // Reemplaza con tu clave API de Google Maps

        $client = new Client();

        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

        $data = [
            'origin' => $recorrido["origin"],
            'destination' => $recorrido["destination"],
            'intermediates' => [],
            "travelMode" => "DRIVE",
            "optimizeWaypointOrder" => true
        ];

        $intermediates = $recorrido["intermediates"];

        $data["intermediates"] = collect($intermediates)->map(function($item){
            return [
                'location' => [
                    'latLng' => [
                        'latitude' => $item["location"]["lat"],
                        'longitude' => $item["location"]["lng"],
                    ],
                ],
            ];
        })->toArray();
      
        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline,routes.optimizedIntermediateWaypointIndex',
        ];

        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $data,
        ]);

        $result = json_decode($response->getBody(), true);
        
        return $result;
    }

    public function updateEstado(Recorrido $recorrido, array $request){

        try {

            $recorrido->recorrido_estado_id = $request["recorrido_estado_id"];
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