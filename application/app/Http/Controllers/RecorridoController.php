<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Recorrido\GenerarInformeRecorridoRequest;
use App\Http\Requests\Recorrido\GetRecorridoRequest;
use App\Http\Requests\Recorrido\OptimizarRecorridoRequest;
use App\Http\Requests\Recorrido\SaveDestinoRequest;
use App\Http\Requests\Recorrido\SaveOrigenRequest;
use App\Http\Requests\Recorrido\SaveRecorridoRequest;
use App\Http\Requests\Recorrido\UpdateEstadoRecorridoRequest;
use App\Http\Requests\Recorrido\UpdateOrigenActualRequest;
use App\Http\Services\Empresa\EmpresaService;
use App\Http\Services\Recorrido\RecorridoService;
use App\Models\Recorrido;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;

class RecorridoController extends Controller
{

    public function __construct(
        public RecorridoService $recorridoService,
        public EmpresaService $empresaService
    ){}

    public function findAll(GetRecorridoRequest $request, int $recorrido_id = null){

        try {

            $parametros = $request->all();
    
            $parametros["recorrido_id"] = $recorrido_id ?? $request->input("recorrido_id");
            $usuario = $request->user();

            $recorridos = $this->recorridoService->findAll(
                parametros: $parametros, 
                permisos: [],
                usuarioAutenticadoId: $usuario->id , 
            );
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($recorridos);
    }

    public function create(SaveRecorridoRequest $request){

        $usuarioAutenticado = $request->user();

        $usuarioId = $request->rider_id;

        try {

            if($usuarioAutenticado->id !== $usuarioId){
                throw new BussinessException(AppErrors::USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::USUARIO_NO_TE_PERTENECE_CODE);
            }
        
            // if(!$this->empresaService->usuarioPerteneceEmpresa($usuarioId, $request->input("empresa_id"))){
            //     throw new BussinessException(AppErrors::EMPRESA_USER_NOT_EXISTS_MESSAGE, AppErrors::EMPRESA_USER_NOT_EXISTS_CODE);
            // }

            $data = [
                "rider_id"      => $usuarioAutenticado->id,
                // "empresa_id"    => $request->input("empresa_id"),
                "inicio"        => $request->input("inicio"),
            ];

            $recorrido = $this->recorridoService->create($data, $usuarioAutenticado->id);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::EMPRESA_USER_NOT_EXISTS_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $recorrido]);
    }

    public function updateOrigen(Recorrido $recorrido, SaveOrigenRequest $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->updateOrigen($request->all(), $recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado","origen_auto"])]);

    }

    public function updateOrigenActual(Recorrido $recorrido, UpdateOrigenActualRequest $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->updateOrigenActual($request->all(), $recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado","origen_auto"])]);

    }

    public function removeOrigen(Recorrido $recorrido, Request $request){
      
        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->removeOrigen($recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado"])]);

    }

    public function removeDestino(Recorrido $recorrido, Request $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->removeDestino($recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado"])]);

    }

    public function updateDestino(Recorrido $recorrido, SaveDestinoRequest $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $destino = $this->recorridoService->updateDestino($request->all(), $recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $destino->only(["id", "destino_lat", "destino_lng", "destino_formateado"])]);

    }

    public function updateEstado(Recorrido $recorrido, UpdateEstadoRecorridoRequest $request){

       try {
            $usuario = $request->user();

            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $recorridoEstado = $this->recorridoService->updateEstado($recorrido, $request->all());
        
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return $recorridoEstado;
    }

    public function optimizar(OptimizarRecorridoRequest $request){
        
        [$recorrido, $distancia, $duracion, $polyline ] = $this->recorridoService->optimizar($request->all());

        return response()->json(compact('recorrido', 'distancia', 'duracion', 'polyline'), 200);
    }

    public function detectarPropiedades(Request $request) {
        try {
           
            $imageAnnotatorClient = new ImageAnnotatorClient([
                'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
              
            ]);
            
    
            $imageContent = file_get_contents($request->file->getPathName());
            $response = $imageAnnotatorClient->textDetection($imageContent);
        
            // Verifica si hay alguna anotación de texto
            if ($response->getTextAnnotations()) {
                // Obtiene la primera anotación de texto (puedes ajustar esto según tus necesidades)
                $textAnnotation = $response->getTextAnnotations()[0];
    
                // Accede al contenido del texto
                $textContent = $textAnnotation->getDescription();
                
                // Define patrones de expresiones regulares para cada propiedad que deseas extraer
                $patterns = [
                    'direccion' => '/Direccion:\s*(.*?)\n/',
                    'destinatario' => '/Destinatario:\s*(.*?)\n/',
                    'telefono' => '/Teléfono:\s*(.*?)\n/',
                    'dni' => '/DNI:\s*(\d+)/',
                    'enviar_a' => '/Enviar a:\s*(.*?)\n/',
                    'envio' => '/Envio:\s*(.*?)\n/',
                    'notas_del_cliente' => '/Notas del cliente:\s*(.*?)\n/',
                    'referencia' => '/Referencia:\s*(.*?)\n/',
                ];

    
                // Inicializa el array de resultados
                $result = [];
    
                // Busca cada propiedad en el texto utilizando las expresiones regulares
                foreach ($patterns as $label => $pattern) {
                   if (preg_match($pattern, $textContent, $matches)) {
                        $result[$label] = $matches[1];
                    }
                }

                if (isset($result['dni'])) {
                    //Si ya encontramos el dni, busca la dirección debajo del dni
                    $dniPattern = '/DNI:\s*\d+\n(.*?)\n/';
                    if (preg_match($dniPattern, $textContent, $dniMatches)) {
                        $result['direccion'] = $dniMatches[1];
                    }
                } 
    
                $imageAnnotatorClient->close();
    
                return response()->json(["propiedades" => $result]);
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

}
