<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Parada\SaveParadaRequest;
use App\Http\Services\Parada\ParadaService;
use App\Models\Parada;
use Illuminate\Http\Request;

class ParadaController extends Controller
{
    public function __construct(
        public ParadaService $paradaService
    ){}

    public function findAll(Request $request, int $parada_id = null){

        try {
       
            $parametros = $request->all();
            $parametros["parada_id"] = $parada_id ?? $request->input("parada_id");
            
            $usuario = $request->user();

            if(!isset($request["parada_id"])){
                // TODO: solo permitir a admin o autorizados para traer todas las paradas
                return response()->json([]);
            }
    
            if(isset($request["parada_id"])){
                $this->validarParadaPerteneceUsuario($usuario->id, $parametros["parada_id"]);

            } 

            $paradas = $this->paradaService->findAll($parametros, userId: $usuario->id , permisos: []);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($paradas);
    }

    public function create(SaveParadaRequest $request){

        try {

            $parada = $this->paradaService->create($request->validated());

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        return response()->json([
            "parada" => $parada
        ]);
    }

    public function update(Parada $parada, SaveParadaRequest $request){

        try {

            $riderId = $request->rider_id;
            $this->validarParadaPerteneceUsuario($riderId, $parada->id);

            $actualizarParada = $this->paradaService->update($request->validated(), $parada);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::PARADA_NO_PERTECE_USUARIO_CODE ? 404 : 400);
        }

        return response()->json([
            "parada" => $actualizarParada
        ]);
    }

    public function updateEstado(Parada $parada, SaveParadaRequest $request){
        try {

            $riderId = $request->rider_id;
            $this->validarParadaPerteneceUsuario($riderId, $parada->id);

            $actualizarParada = $this->paradaService->updateEstado($request->validated(), $parada);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::PARADA_NO_PERTECE_USUARIO_CODE ? 404 : 400);
        }

        return response()->json([
            "parada" => $actualizarParada
        ]);
    }

    public function delete(Parada $parada, Request $request){

        try {

            $riderId = $request->user()->id;
            $this->validarParadaPerteneceUsuario($riderId, $parada->id);

            $parada->delete();

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::PARADA_NO_PERTECE_USUARIO_CODE ? 404 : 400);
        }

        return response()->json([
            "id" => $parada->id
        ]);
    }

    private function validarParadaPerteneceUsuario(int $riderId, int $parada_id){
        if(!$this->paradaService->perteneceUsuario($riderId, $parada_id)){
            throw new BussinessException(AppErrors::PARADA_NO_PERTECE_USUARIO_MESSAGE, AppErrors::PARADA_NO_PERTECE_USUARIO_CODE);
        }
    }
    
}
