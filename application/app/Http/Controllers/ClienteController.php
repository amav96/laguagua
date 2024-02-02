<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Cliente\SaveClienteRequest;
use App\Http\Services\Cliente\ClienteService;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(
        public ClienteService $clienteService
    )
    {}

    public function findAll(Request $request, int $cliente_id = null){

        try {

            $params = $request->all();
            $params["cliente_id"] = $cliente_id ?? $request->input("cliente_id");

            $clientes = $this->clienteService->findAll($params);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($clientes);
    }

    public function create(SaveClienteRequest $request){

        try {

            $usuario = $request->user();

            $cliente = $this->clienteService->create($request->all(), $usuario->id);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }
        
        return response()->json($cliente);
    }

    public function update(Cliente $cliente, SaveClienteRequest $request){

        try {
            $usuario = $request->user();
            $this->validarQuienActualiza($cliente, $usuario->id);
           
            $cliente = $this->clienteService->update($cliente, $request->all());

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }
        
        return response()->json($cliente);

    }

    private function validarQuienActualiza(Cliente $cliente, int $usuarioId){
        if($cliente->creado_por !== $usuarioId){
            throw new BussinessException(
                AppErrors::CLIENTE_USUARIO_NO_PERTENECE_ERROR_MESSAGE, 
                AppErrors::CLIENTE_USUARIO_NO_PERTENECE_ERROR_CODE);
        }
    }
}
