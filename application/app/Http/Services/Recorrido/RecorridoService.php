<?php


namespace App\Http\Services\Recorrido;

use App\Models\Recorrido;
use App\Models\EstadoRecorrido;
use App\Models\User;
use GuzzleHttp\Client;

class RecorridoService {

    public function create(User $usuario, int $empresaId){

        return Recorrido::create([
            "rider_id"            => $usuario->id,
            "empresa_id"            => $empresaId,
            "estado_recorrido_id"   => EstadoRecorrido::PREPARADO
        ]);
    }

    public function updateOrigen(array $request, int $recorridoId) : Recorrido {
 
        $recorrido = Recorrido::find($recorridoId);
        $recorrido->origen_lat = $request["origen_lat"];
        $recorrido->origen_lng = $request["origen_lng"];
        $recorrido->origen_formateado = $request["origen_formateado"];
        $recorrido->optimizado = 0;
        $recorrido->save();

        return $recorrido;
    }

    public function updateDestino(array $request, int $recorridoId) : Recorrido {
 
        $recorrido = Recorrido::find($recorridoId);
        $recorrido->destino_lat = $request["destino_lat"];
        $recorrido->destino_lng = $request["destino_lng"];
        $recorrido->destino_formateado = $request["destino_formateado"];
        $recorrido->optimizado = 0;
        $recorrido->save();
        
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
    

    public function perteneceUsuario(int $riderId, int $recorridoId) : bool {
        return Recorrido::where("rider_id", $riderId)->where("id", $recorridoId)->exists();
    }

    public function existeRecorrido($recorrido_id){

        return Recorrido::where("id",$recorrido_id)->exists();
        
    }

}