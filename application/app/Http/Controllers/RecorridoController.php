<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Recorrido\SaveRecorridoRequest;
use App\Http\Services\Empresa\EmpresaService;
use App\Http\Services\Google\RecorridoService;
use Illuminate\Http\Request;


class RecorridoController extends Controller
{

    public function __construct(
        public RecorridoService $recorridoService,
        public EmpresaService $empresaService
    ){}

    public function create(SaveRecorridoRequest $request){

        $usuario = $request->user();

        try {
        
            if(!$this->empresaService->usuarioPerteneceEmpresa($usuario->id, $request->input("empresa_id"))){
                throw new BussinessException(AppErrors::EMPRESA_USER_NOT_EXISTS_MESSAGE, AppErrors::EMPRESA_USER_NOT_EXISTS_CODE);
            }

            $recorrido = $this->recorridoService->create($request->user(), $request->input("empresa_id"));

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::EMPRESA_USER_NOT_EXISTS_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $recorrido]);
    }

    public function armarRecorrido(SaveRecorridoRequest $request){
       
        $recorrido = $this->recorridoService->obtenerRecorrido($request->all());

        return response()->json(['recorrido' => $recorrido], 200);
    }
}
