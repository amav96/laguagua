<?php


namespace App\Http\Services\Recorrido;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Recorrido;
use App\Models\EstadoRecorrido;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;

class RecorridoService {

    public function findAll(array $filtros, array $relaciones = [], array $permisos = [], int $usuarioAutenticadoId) {

        $query = Recorrido::query();
     
        $query = $query
                ->with($relaciones)
                ->when(isset($filtros["recorrido_id"]), function (Builder $q) use($filtros) : void {
                    $q->where('id', $filtros["recorrido_id"]); 
                })
                ->when(isset($filtros["inicio"]), function (Builder $q) use($filtros) : void {
                    $q->whereDate('inicio', '>=', $filtros["inicio"] . ' 00:00:00')
                      ->where('inicio', '<=', $filtros["inicio"] . ' 23:59:59');
                })
                ->when(isset($filtros["rider_id"]), function (Builder $q) use($filtros) : void {
                    $q->where('rider_id', $filtros["rider_id"]); 
                })
                ->when(count($permisos) === 0, function (Builder $q) use($filtros, $usuarioAutenticadoId) : void {
                    $q->where('rider_id', $usuarioAutenticadoId); 
                });

        if(isset($filtros["page"])){
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
                "estado_recorrido_id"   => EstadoRecorrido::PREPARADO,
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

            $recorrido->estado_recorrido_id = $request["estado_recorrido_id"];
            $recorrido->save();

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_ACTUALIZAR_ERROR_CODE);
        }

        return $recorrido->load(['estadoRecorrido']);
    }
    
    public function perteneceUsuario(int $riderId, int $recorridoId) : bool {
        return Recorrido::where("rider_id", $riderId)->where("id", $recorridoId)->exists();
    }

    public function existeRecorrido($recorrido_id){
        return Recorrido::where("id",$recorrido_id)->exists();
    }

}