<?php

namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Models\ItemComprobante;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\BussinessException;


class ItemComprobanteService {

    public function generarUrlTemporaria(array $request, int $usuarioId){

        beginTransaction();
        try {

            $storage = Storage::temporaryUploadUrl(
                $request["nombre_archivo"], now()->addMinutes(5)
            );
            
            $comprobante = ItemComprobante::create([
                "item_id"       => $request["item_id"],
                "usuario_id"    => $usuarioId
            ]);


        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::COMPROBANTE_ITEM_CREAR_ERROR_MESSAGE, AppErrors::COMPROBANTE_ITEM_CREAR_ERROR_CODE);
        }
        
        commit();

    
        return [
            "comprobante" => $comprobante,
            "storage" => $storage
        ];
    }

    public function updateComprobante(ItemComprobante $itemComprobante, array $request) :ItemComprobante {

        beginTransaction();
        try {

            $itemComprobante->path = $request["path"];
            $itemComprobante->save();

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::COMPROBANTE_ITEM_ACTUALIZAR_ERROR_MESSAGE, AppErrors::COMPROBANTE_ITEM_ACTUALIZAR_ERROR_CODE);
        }
        
        commit();
        return $itemComprobante;

    }

}