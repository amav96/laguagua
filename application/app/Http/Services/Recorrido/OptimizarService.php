<?php

namespace App\Http\Services\Recorrido;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Services\ConsumoService;
use App\Http\Services\FlexiblePolyline\FlexiblePolyline;
use App\Models\Recorrido;
use Carbon\Carbon;
use GuzzleHttp\Client;

use Illuminate\Database\Eloquent\Collection;


class OptimizarService {

    protected Collection $paradas;
    protected float $origenLat;
    protected float $origenLng;
    protected float $destinoLat;
    protected float $destinoLng;
    protected int $usuarioId;

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

    public function setUsuarioId(int $usuarioId){
        $this->usuarioId = $usuarioId;
    }

    public function optimizar(){
        
        try {
            
            $paradasOptimizar = $this->paradas->filter(function ($parada) {
                return isset($parada['paradaEstado']) && $parada['paradaEstado']['codigo'] !== 'visitado' && $parada['paradaEstado']['tipo'] !== 'negativo';
            })->values();

            $paradasRestantes = $this->paradas->reject(function ($parada) use ($paradasOptimizar) {
                return $paradasOptimizar->contains($parada);
            })->values()->sortByDesc('realizado_en');

            if($paradasOptimizar->count() <= 25){
                list($paradasOptimizadas, $distancia, $duracion, $polyline) = $this->GOOGLEOptimizador($paradasOptimizar, $paradasRestantes);
            } else {
                list($paradasOptimizadas, $distancia, $duracion, $polyline) = $this->HEREOptimizador($paradasOptimizar, $paradasRestantes);
            }
            

        } catch (\Throwable $th) {
           
            throw new BussinessException(AppErrors::RECORRIDO_OPTIMIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_OPTIMIZAR_ERROR_CODE);
        }
        return [
            $paradasOptimizadas->sortBy('orden')->concat($paradasRestantes)->values(),
            $distancia,
            $duracion,
            $polyline
        ];
    }

    public function HEREOptimizador(Collection $paradas, Collection $paradasRestantes): array {
      
        try {
            $apiKey = config('app')["values"]["HERE_API_KEY"]; 
        
            $client = new Client();

            $url = 'https://wps.hereapi.com/v8/findsequence2';
        
            // Construir los datos de la solicitud
            
            $data = [
                'start' => $this->origenLat.','.$this->origenLng,
                'end' => $this->destinoLat.','.$this->destinoLng,
                'mode' => 'car',
                'improveFor'    => 'distance',
                'departure' => Carbon::now()->toIso8601String(),
            
            ];

            foreach ($paradas as $key => $parada) {
                $data["destination".$key + 1] = $parada->id.';'.$parada->lat.','.$parada->lng;
            }

            $response = $client->get($url.'?&'.http_build_query($data).'&apiKey='.$apiKey);

            $result = json_decode($response->getBody(), true);
        
            $distancia = $this->convertirDistancia($result["results"][0]["distance"]);
            $duracion = $this->convertirDuracion($result["results"][0]["time"]);
        
            if(isset($result["results"][0])){
                $ultimoNumeroParadaOrden = 0;
                $ordenParadas = $result["results"][0]["waypoints"];
            
                foreach($ordenParadas as $posicionParada => $parada ){
                    if($parada["id"] !== "start" && $parada["id"] !== "end"){
                        $indexParada = $paradas->search((function($p) use($parada){
                            return $p->id == $parada["id"];
                        }));
                        if(gettype($indexParada) === 'integer'){
                            $paradas[$indexParada]->orden = $parada["sequence"];
                            $paradas[$indexParada]->save();
                            $ultimoNumeroParadaOrden = $parada["sequence"];
                        }
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
                $consumoService->guardarConsumoOptimizar($this->usuarioId, '0,0055');
            }

        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_OPTIMIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_OPTIMIZAR_ERROR_CODE);
        }


        return [
            $paradas,
            $distancia,
            $duracion,
            null
        ];

    }

    public function GOOGLEOptimizador(Collection $paradas, Collection $paradasRestantes){

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

  
            $data["intermediates"] = $paradas->map(function($item){
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
            $duracion = $this->convertirDuracion($result["routes"][0]["duration"], '/(\d+)s/');
            $polyline = $result["routes"][0]["polyline"]["encodedPolyline"];
            
            if(isset($result["routes"][0])){
                $ultimoNumeroParadaOrden = 0;
                $ordenParadas = $result["routes"][0]["optimizedIntermediateWaypointIndex"];
                foreach($ordenParadas as $posicionParada => $indexParada ){
                    if(isset($paradas[$indexParada])){
                    
                        $paradas[$indexParada]->orden = $posicionParada + 1;
                        $paradas[$indexParada]->save();
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
                $consumoService->guardarConsumoOptimizar($this->usuarioId, count($ordenParadas), $paradas->count() > 12 ? 0.008 : 0.004);
            }
        } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::RECORRIDO_OPTIMIZAR_ERROR_MESSAGE, AppErrors::RECORRIDO_OPTIMIZAR_ERROR_CODE);
        }
        return [
            $paradas,
            $distancia,
            $duracion,
            $polyline
        ];

    }

    private function convertirDistancia($metros)
    {
        if ($metros >= 1000) {
            return round($metros / 1000, 2) . " km";
        } else {
            return round($metros) . " metros";
        }
    }

    private function convertirDuracion($duracion, $expresion = null)
    {
        if($expresion){
            preg_match($expresion, $duracion, $matches);
            if (!empty($matches[1])) {
                $segundos = (int)$matches[1];
            }
        }
         else {
            $segundos = $duracion;
        }

        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);

        if ($horas > 0) {
            return "$horas h " . ($minutos > 0 ? "$minutos min" : "");
        } else {
            return "$minutos min";
        }

        return $duracion;
    }

    public function polyline(){

        $paradasRecorrido = $this->paradas->filter(function ($parada) {
            return isset($parada['paradaEstado']) && $parada['paradaEstado']['codigo'] !== 'visitado' && $parada['paradaEstado']['tipo'] !== 'negativo';
        })->values()->sortBy("orden");

        $data = [
            "transportMode" => "car",
            "origin"    => $this->origenLat.','.$this->origenLng,
            "destination"    => $this->destinoLat.','.$this->destinoLng,
            "return"    => "polyline"
        ];

        $params = http_build_query($data);
       
        foreach($paradasRecorrido as $parada){
            $viaParams[] = 'via='.$parada->lat.','.$parada->lng;
        }
       
        $apiKey = config('app')["values"]["HERE_API_KEY"]; 
        $client = new Client();
        $url = 'https://router.hereapi.com/v8/routes';
        
        $response = $client->get($url.'?'.$params.'&'.implode('&', $viaParams).'&apiKey='.$apiKey);
        
        $result = json_decode($response->getBody(), true);
       
        $polylinePoints = [];
        foreach ($result['routes'][0]['sections'] as $section) {
            $polyline = $section['polyline'];
            $decodedPolyline = FlexiblePolyline::decode($polyline);
            $polylinePoints = array_merge($polylinePoints, $decodedPolyline['polyline']);
        }

        $consumoService =  new ConsumoService();
        $consumoService->guardarConsumoPolyline($this->usuarioId, '0,00083');
        
        // RENDERIZA EN GOOGLE MAPS
        return [$this->encodePolyline($polylinePoints)]; 

        // RENDERIZA EN HERE MAPS
        // return [FlexiblePolyline::encode($polylinePoints)];
    }

    private function encodePolyline($coordinates) {
        // encode para google maps

        $encoded = '';
        $prevLat = 0;
        $prevLng = 0;
        
        foreach ($coordinates as $coordinate) {
            $lat = $coordinate[0];
            $lng = $coordinate[1];
    
            $latDiff = round(($lat - $prevLat) * 1e5);
            $lngDiff = round(($lng - $prevLng) * 1e5);
    
            $encoded .= $this->encodeValue($latDiff) . $this->encodeValue($lngDiff);
    
            $prevLat = $lat;
            $prevLng = $lng;
        }
    
        return $encoded;
    }
    
    private function encodeValue($value) {
        // encode para google maps
        $value <<= 1;
        if ($value < 0) {
            $value = ~$value;
        }
    
        $encoded = '';
        while ($value >= 0x20) {
            $encoded .= chr((0x20 | ($value & 0x1f)) + 63);
            $value >>= 5;
        }
        $encoded .= chr($value + 63);
        return $encoded;
    }


}