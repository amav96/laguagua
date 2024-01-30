<?php


namespace App\Http\Services\Recorrido;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Recorrido;
use App\Models\RecorridoEstado;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use PDF;

class RecorridoService {

    public function findAll(array $parametros, array $permisos = [], int $usuarioAutenticadoId) {

        $query = Recorrido::query();
       
        $query = $query
                ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                    $q->with(explode(",", $parametros["incluir"]));
                })
                ->when(isset($parametros["incluir"]) && strpos($parametros["incluir"], "paradas") !== false, function (Builder $q) : void {
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
    
    public function optimizar(array $request): array{
         
        $apiKey = config('app')["values"]["GOOGLE_API_KEY"]; 

        $client = new Client();

        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';
        
        $recorrido = Recorrido::with('paradas.paradaEstado')->findOrFail($request["recorrido_id"]);
       
        $data = [
            'origin' => [
                "location" => [
                    "latLng" => [
                        "latitude"  => $recorrido->origen_actual_lat,
                        "longitude"  => $recorrido->origen_actual_lng,
                    ]
                ]
            ],
            'destination' => [
                "location" => [
                    "latLng" => [
                        "latitude"  => $recorrido->destino_lat,
                        "longitude"  => $recorrido->destino_lng,
                    ]
                ]
            ],
            'intermediates' => [],
            "travelMode" => "DRIVE",
            "optimizeWaypointOrder" => true
        ];

        $paradasOptimizar = $recorrido->paradas->filter(function ($parada) {
            return isset($parada['paradaEstado']) && $parada['paradaEstado']['codigo'] !== 'visitado' && $parada['paradaEstado']['tipo'] !== 'negativo';
        })->values();

        $paradasRestantes = $recorrido->paradas->reject(function ($parada) use ($paradasOptimizar) {
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
            foreach($result["routes"][0]["optimizedIntermediateWaypointIndex"] as $posicionParada => $indexParada ){
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
        }

        $recorrido->optimizado = 1;
        $recorrido->distancia = $distancia;
        $recorrido->duracion = $duracion;
        $recorrido->polyline = $polyline;
        $recorrido->save();
        
        return [
            $paradasOptimizar->sortBy('orden')->concat($paradasRestantes)->values(),
            $distancia,
            $duracion,
            $polyline
        ];
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

}