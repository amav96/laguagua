<?php 

namespace App\Http\Services\Google;

use App\Models\Empresa;
use App\Models\EstadoRecorrido;
use App\Models\Recorrido;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;

class RecorridoService {

    public function create(User $usuario, int $empresaId){

        return Recorrido::create([
            "usuario_id"            => $usuario->id,
            "empresa_id"            => $empresaId,
            "estado_recorrido_id"   => EstadoRecorrido::PREPARADO
        ]);
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
}