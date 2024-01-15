<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Parada\DeleteParadaRequest;
use App\Http\Requests\Parada\SaveParadaRequest;
use App\Http\Services\Parada\ParadaService;
use App\Models\Parada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ParadaController extends Controller
{
    public function __construct(
        public ParadaService $paradaService
    ){}

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

            if(!$this->paradaService->perteneceUsuario($riderId, $parada->id)){
                throw new BussinessException(AppErrors::PARADA_NO_PERTECE_USUARIO_MESSAGE, AppErrors::PARADA_NO_PERTECE_USUARIO_CODE);
            }

            $actualizarParada = $this->paradaService->update($request->validated(), $parada);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::PARADA_NO_PERTECE_USUARIO_CODE ? 404 : 400);
        }

        return response()->json([
            "parada" =>$actualizarParada
        ]);
    }

    public function delete(Parada $parada, Request $request){

        $riderId = $request->user()->id;

        try {

            if(!$this->paradaService->perteneceUsuario($riderId, $parada->id)){
                throw new BussinessException(AppErrors::PARADA_NO_PERTECE_USUARIO_MESSAGE, AppErrors::PARADA_NO_PERTECE_USUARIO_CODE);
            }
    
            $parada->delete();

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::PARADA_NO_PERTECE_USUARIO_CODE ? 404 : 400);
        }

        return response()->json([
            "id" => $parada->id
        ]);
    }
}
