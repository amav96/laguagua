<?php

namespace App\Http\Controllers;

use App\Exceptions\BussinessException;
use Illuminate\Http\Request;
use App\Http\Requests\Item\SaveComprobanteItemRequest;
use App\Http\Services\Item\ComprobanteItemService;
use App\Http\Services\Item\ItemService;
use App\Models\ComprobanteItem;

class ComprobanteItemController extends Controller
{

    public function __construct(
        public ComprobanteItemService $comprobanteItemService
    ){}


    public function generarUrlTemporaria(SaveComprobanteItemRequest $request){

        try {

            $usuario = $request->user();

            $comprobante = $this->comprobanteItemService->generarUrlTemporaria($request->all(), $usuario->id);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($comprobante);
        
    }

    public function updateComprobante(ComprobanteItem $comprobanteItem,  SaveComprobanteItemRequest $request){
     
        try {
            $comprobante = $this->comprobanteItemService->updateComprobante($comprobanteItem, $request->all());
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($comprobante);
    }
}
