<?php

namespace App\Http\Controllers;

use App\Exceptions\BussinessException;
use App\Http\Services\Item\ItemEstadoService;
use Illuminate\Http\Request;

class ItemEstadoController extends Controller
{

    public function __construct(
        public ItemEstadoService $itemEstadoService
    )
    {}

    public function findAll(Request $request){

        try {
       
            $parametros = $request->all();
            $paradas = $this->itemEstadoService->findAll($parametros);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($paradas);

    }
}
