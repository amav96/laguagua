<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
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
}
