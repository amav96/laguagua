<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Item\SaveItemRequest;
use App\Http\Services\Item\ItemService;
use App\Http\Services\Parada\ParadaService;
use App\Models\Item;
use Illuminate\Http\Request;


class ItemController extends Controller
{
    public function __construct(
        public ParadaService $paradaService,
        public ItemService $itemService
    ){}

    public function findAll(Request $request, int $item_id = null){

        try {
       
            $parametros = $request->all();
            $parametros["item_id"] = $item_id ?? $request->input("item_id");
            
            $usuario = $request->user();

            if(!isset($request["item_id"])){
                // TODO: solo permitir a admin o autorizados para traer todos los items
                return response()->json([]);
            }

            $paradas = $this->itemService->findAll($parametros, userId: $usuario->id , permisos: []);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($paradas);
    }

    public function create(SaveItemRequest $request){

        $this->validarParada();

        try {

            $usuario = $request->user();

            $item = $this->itemService->create($request->all(), $usuario->id);
                
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }   
        
        return response()->json($item);
    }

    public function update(Item $item, SaveItemRequest $request){

        $this->validarParada();

        try {

            $usuario = $request->user();

            $this->validarCreadorItem($usuario->id, $item);
    
            $actualizarItem = $this->itemService->update($item, $request->all());

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }   


        return response()->json($actualizarItem);
        
    }

    public function updateEstado(Item $item, SaveItemRequest $request){

        try {

            $usuario = $request->user();

            $this->validarCreadorItem($usuario->id, $item);

            $actualizarItem = $this->itemService->updateEstado($item, $request->all());

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($actualizarItem);
    }

    private function validarParada(){
        if(isset($request["parada_id"]) && isset($request["rider_id"])  && !$this->paradaService->perteneceUsuario($request["rider_id"], $request["parada_id"])){
            throw new BussinessException(AppErrors::PARADA_NO_PERTECE_USUARIO_MESSAGE, AppErrors::PARADA_NO_PERTECE_USUARIO_CODE);
        }
    }

    private function validarCreadorItem(int $usuarioId, Item $item){
        if($usuarioId !== $item->creado_por){
            throw new BussinessException(AppErrors::ITEM_NO_PERTECE_USUARIO_MESSAGE, AppErrors::ITEM_NO_PERTECE_USUARIO_CODE);
        }
    }

}
