<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Item\SaveItemRequest;
use App\Http\Services\Item\ItemService;
use App\Http\Services\Parada\ParadaService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        public ParadaService $paradaService,
        public ItemService $itemService
    ){}

    public function create(SaveItemRequest $request){

        $this->validarParada();

        $itemsCreados = [];

        try {
            $data = $request->all();
            $items =  $data["items"];
           
            if($items && count($items) > 0){
                foreach($items as $item){

                    $item["parada_id"] = $data["parada_id"];

                    $crearItem = $this->itemService->create($item, $data["parada_id"]);
                    if($crearItem){
                        $itemsCreados[] = $crearItem;
                    }
                }
            }  
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }   
        
        return response()->json($itemsCreados);
    }

    private function validarParada(){
        if(isset($request["parada_id"]) && isset($request["rider_id"])  && !$this->paradaService->perteneceUsuario($request["rider_id"], $request["parada_id"])){
            throw new BussinessException(AppErrors::PARADA_NO_PERTECE_USUARIO_MESSAGE, AppErrors::PARADA_NO_PERTECE_USUARIO_CODE);
        }
    }
}
