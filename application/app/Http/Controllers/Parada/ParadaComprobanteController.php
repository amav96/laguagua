<?php

namespace App\Http\Controllers\Parada;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Parada\SaveParadaComprobanteRequest;
use App\Http\Services\Parada\ParadaComprobanteService;
use App\Models\ParadaComprobante;
use Illuminate\Http\Request;

class ParadaComprobanteController extends Controller
{
    public function __construct(
        public ParadaComprobanteService $paradaComprobanteService
    ){}


    public function generarUrlTemporaria(SaveParadaComprobanteRequest $request){

        try {

            $usuario = $request->user();

            $comprobante = $this->paradaComprobanteService->generarUrlTemporaria($request->all(), $usuario->id);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($comprobante);
        
    }

    public function delete(ParadaComprobante $paradaComprobante, Request $request){

        try {

            $usuario = $request->user();

            $eliminado = $this->paradaComprobanteService->delete($paradaComprobante);

            if($paradaComprobante->usuario_id !== $usuario->id){
                throw new BussinessException(AppErrors::COMPROBANTE_PARADA_NO_TE_PERTENECE_MESSAGE, AppErrors::COMPROBANTE_PARADA_NO_TE_PERTENECE_CODE);
            }
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json(["id" => $eliminado->id]);
    }
}
