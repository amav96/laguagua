<?php

namespace App\Http\Services\Recorrido;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Services\ConsumoService;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;

class OptimizarService {

    protected Collection $paradas;
    protected float $origenLat;
    protected float $origenLng;
    protected float $destinoLat;
    protected float $destinoLng;
    protected int $recorridoId;
    protected int $usuarioId;
    protected int $paradasPorBloque = 4;


    public function optimizar(){
        
        try {
            $apiKey = config('app')["values"]["GOOGLE_API_KEY"]; 
       
            $client = new Client();

            $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';
            
            $data = [
                'origin' => [
                    "location" => [
                        "latLng" => [
                            "latitude"  => $this->origenLat,
                            "longitude"  => $this->origenLng,
                        ]
                    ]
                ],
                'destination' => [
                    "location" => [
                        "latLng" => [
                            "latitude"  => $this->destinoLat,
                            "longitude"  => $this->destinoLng,
                        ]
                    ]
                ],
                'intermediates' => [],
                "travelMode" => "DRIVE",
                "optimizeWaypointOrder" => true
            ];

            $paradasOptimizar = $this->paradas->filter(function ($parada) {
                return isset($parada['paradaEstado']) && $parada['paradaEstado']['codigo'] !== 'visitado' && $parada['paradaEstado']['tipo'] !== 'negativo';
            })->values();

            $puntoInicialPrimerBloque = [
                "lat" => $this->origenLat,
                "lng" => $this->origenLng
            ];

            $bloquesParadas = $this->bloquesParadas($paradasOptimizar->toArray(), $puntoInicialPrimerBloque);
            dd($bloquesParadas);
            
            $paradasRestantes = $this->paradas->reject(function ($parada) use ($paradasOptimizar) {
                return $paradasOptimizar->contains($parada);
            })->values()->sortByDesc('realizado_en');
        
            $data["intermediates"] = $paradasOptimizar->map(function($item){
                return [
                    'location' => [
                        'latLng' => [
                            'latitude' => $item->lat,
                            'longitude' => $item->lng,
                        ],
                    ],
                ];
            })->values()->toArray();

            
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

            $distancia = $this->convertirDistancia($result["routes"][0]["distanceMeters"]);
            $duracion = $this->convertirDuracion($result["routes"][0]["duration"]);
            $polyline = $result["routes"][0]["polyline"]["encodedPolyline"];
            
            if(isset($result["routes"][0])){
                $ultimoNumeroParadaOrden = 0;
                $ordenParadas = $result["routes"][0]["optimizedIntermediateWaypointIndex"];
                foreach($ordenParadas as $posicionParada => $indexParada ){
                    if(isset($paradasOptimizar[$indexParada])){
                    
                        $paradasOptimizar[$indexParada]->orden = $posicionParada;
                        $paradasOptimizar[$indexParada]->save();
                        $ultimoNumeroParadaOrden = $posicionParada;
                    }
                }
                if(!$paradasRestantes->isEmpty()){
                    foreach($paradasRestantes as $paradaRestante){
                        $ultimoNumeroParadaOrden++;
                        $paradaRestante->orden = $ultimoNumeroParadaOrden;
                        $paradaRestante->save();
                    }
                }

                $consumoService =  new ConsumoService();
                $consumoService->guardarConsumoOptimizar($this->usuarioId, count($ordenParadas));
            }
        } catch (\Throwable $th) {
            dd($th);
            throw new BussinessException(AppErrors::RECORRIDO_OPTIMIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_OPTIMIZAR_ERROR_CODE);
        }
        return [
            $paradasOptimizar->sortBy('orden')->concat($paradasRestantes)->values(),
            $distancia,
            $duracion,
            $polyline
        ];
    }

    private function bloquesParadas(array $paradas, array $puntoInicialPrimerBloque) : array {
        $bloques = [];
        $paradasNoAsignadas = $paradas;
        $puntoDeReferencia = $puntoInicialPrimerBloque; // Inicializar con el punto inicial del primer bloque
    
        while (!empty($paradasNoAsignadas)) {
            $bloque = $this->generarBloque($paradasNoAsignadas, $puntoDeReferencia);
            $bloques[] = $bloque;
    
            // Actualizar el punto de referencia para el siguiente bloque
            $puntoDeReferencia = end($bloque); // La última parada del bloque actual es el punto de referencia del siguiente bloque
    
            // Eliminar los elementos de $bloque de $paradasNoAsignadas
            foreach ($bloque as $parada) {
                foreach ($paradasNoAsignadas as $key => $p) {
                    if ($parada['id'] === $p['id']) {
                        unset($paradasNoAsignadas[$key]);
                        break; // Salir del bucle interior una vez que se ha encontrado y eliminado el elemento
                    }
                }
            }
        }
    
        return $bloques;
    }
    
    private function generarBloque(array &$paradasNoAsignadas, array $puntoDeReferencia) : array {
        $bloque = [];
        $paradaInicial = null;
        $distanciaExtrema = PHP_FLOAT_MAX; // Inicializar con la distancia máxima
    
        // Buscar la parada más cercana al punto de referencia actual
        foreach ($paradasNoAsignadas as $key => $parada) {
            $distancia = $this->calcularDistanciaEntreParadas(
                $puntoDeReferencia['lat'],    // Latitud del punto de referencia
                $puntoDeReferencia['lng'],    // Longitud del punto de referencia
                $parada['lat'],               // Latitud de la parada actual
                $parada['lng']                // Longitud de la parada actual
            );
    
            if ($distancia < $distanciaExtrema) {
                $paradaInicial = $parada;
                $distanciaExtrema = $distancia;
                $keyToDelete = $key; // Guardar la clave del elemento a eliminar
            }
        }
    
        // Agregar la parada inicial al bloque
        $bloque[] = $paradaInicial;
    
        // Eliminar la parada inicial del array $paradasNoAsignadas
        unset($paradasNoAsignadas[$keyToDelete]);
    
        // Actualizar el punto de referencia para las siguientes paradas
        $puntoDeReferencia = $paradaInicial;
    
        // Continuar llenando el bloque con las paradas restantes
        while (count($bloque) < $this->paradasPorBloque && !empty($paradasNoAsignadas)) {
            // Resto de la lógica sigue igual, pero ahora la parada anterior es la última del bloque
            $paradaMasCercana = null;
            $distanciaMinima = PHP_FLOAT_MAX;
    
            foreach ($paradasNoAsignadas as $key => $parada) {
                $distancia = $this->calcularDistanciaEntreParadas(
                    $bloque[count($bloque) - 1]['lat'], // Latitud de la última parada en el bloque
                    $bloque[count($bloque) - 1]['lng'], // Longitud de la última parada en el bloque
                    $parada['lat'],                      // Latitud de la parada actual
                    $parada['lng']                       // Longitud de la parada actual
                );
    
                if ($distancia < $distanciaMinima) {
                    $paradaMasCercana = $parada;
                    $distanciaMinima = $distancia;
                    $keyToDelete = $key; // Guardar la clave del elemento a eliminar
                }
            }
    
            // Agregar la parada más cercana al bloque
            $bloque[] = $paradaMasCercana;
            
            // Eliminar el elemento del array original
            unset($paradasNoAsignadas[$keyToDelete]);
        }
    
        return $bloque;
    }

    private function calcularDistanciaEntreParadas($lat1, $lng1, $lat2, $lng2) {
        $radio_tierra = 6371; // Radio de la Tierra en kilómetros
        $delta_lat = deg2rad($lat2 - $lat1);
        $delta_lon = deg2rad($lng2 - $lng1);
        $a = sin($delta_lat / 2) * sin($delta_lat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($delta_lon / 2) * sin($delta_lon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distancia = $radio_tierra * $c;
        return $distancia;
    }
    

    private function convertirDistancia($metros)
    {
        if ($metros >= 1000) {
            return round($metros / 1000, 2) . " km";
        } else {
            return round($metros) . " metros";
        }
    }

    private function convertirDuracion($duracion)
    {
        // Extraer el número de segundos utilizando expresiones regulares
        preg_match('/(\d+)s/', $duracion, $matches);

        if (!empty($matches[1])) {
            $segundos = (int)$matches[1];

            $horas = floor($segundos / 3600);
            $minutos = floor(($segundos % 3600) / 60);

            if ($horas > 0) {
                return "$horas h " . ($minutos > 0 ? "$minutos min" : "");
            } else {
                return "$minutos min";
            }
        }

        return $duracion;
    }

    public function setParadas(Collection $paradas){
        $this->paradas = $paradas;
    }

    public function setOrigenLat(float $origenLat){
        $this->origenLat = $origenLat;
    }

    public function setOrigenLng(float $origenLng){
        $this->origenLng = $origenLng;
    }

    public function setDestinoLat(float $destinoLat){
        $this->destinoLat = $destinoLat;
    }

    public function setDestinoLng(float $destinoLng){
        $this->destinoLng = $destinoLng;
    }

    public function setRecorridoId(int $recorridoId){
        $this->recorridoId = $recorridoId;
    }

    public function setUsuarioId(int $usuarioId){
        $this->usuarioId = $usuarioId;
    }
}