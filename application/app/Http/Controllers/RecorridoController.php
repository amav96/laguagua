<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Recorrido\SaveDestinoRequest;
use App\Http\Requests\Recorrido\SaveOrigenRequest;
use App\Http\Requests\Recorrido\SaveRecorridoRequest;
use App\Http\Services\Empresa\EmpresaService;
use App\Http\Services\Recorrido\RecorridoService;
use App\Models\Recorrido;

class RecorridoController extends Controller
{

    public function __construct(
        public RecorridoService $recorridoService,
        public EmpresaService $empresaService
    ){}

    public function create(SaveRecorridoRequest $request){

        $usuarioAutenticado = $request->user();

        $usuarioId = $request->rider_id;

        try {

            if($usuarioAutenticado->id !== $usuarioId){
                throw new BussinessException(AppErrors::USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::USUARIO_NO_TE_PERTENECE_CODE);
            }
        
            if(!$this->empresaService->usuarioPerteneceEmpresa($usuarioId, $request->input("empresa_id"))){
                throw new BussinessException(AppErrors::EMPRESA_USER_NOT_EXISTS_MESSAGE, AppErrors::EMPRESA_USER_NOT_EXISTS_CODE);
            }

            $recorrido = $this->recorridoService->create($request->user(), $request->input("empresa_id"));

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



    public function armarRecorrido(SaveRecorridoRequest $request){
       
        $recorrido = $this->recorridoService->obtenerRecorrido($request->all());

        return response()->json(['recorrido' => $recorrido], 200);
    }
}
