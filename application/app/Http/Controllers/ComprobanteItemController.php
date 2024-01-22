<?php

namespace App\Http\Controllers;

use App\Exceptions\BussinessException;
use App\Http\Requests\Item\SaveItemComprobanteRequest;
use App\Http\Services\Item\ItemComprobanteService;
use App\Models\ItemComprobante;

class ItemComprobanteController extends Controller
{

    public function __construct(
        public ItemComprobanteService $itemComprobanteService
    ){}


    public function generarUrlTemporaria(SaveItemComprobanteRequest $request){

        try {

            $usuario = $request->user();

            $comprobante = $this->itemComprobanteService->generarUrlTemporaria($request->all(), $usuario->id);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($comprobante);
        
    }

    public function updateComprobante(ItemComprobante $itemComprobante,  SaveItemComprobanteRequest $request){
     
        try {
            $comprobante = $this->itemComprobanteService->updateComprobante($itemComprobante, $request->all());
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($comprobante);
    }
}
