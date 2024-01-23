<?php

namespace App\Http\Controllers;

use App\Exceptions\BussinessException;
use App\Http\Services\Parada\ParadaEstadoService;
use App\Models\ParadaEstado;
use Illuminate\Http\Request;

class ParadaEstadoController extends Controller
{

    public function __construct(
        public ParadaEstadoService $paradaEstadoService
    )
    {}

    public function findAll(Request $request){

        try {
       
            $parametros = $request->all();
            $paradasEstadps = $this->paradaEstadoService->findAll($parametros);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($paradasEstadps);

    }

 
}
