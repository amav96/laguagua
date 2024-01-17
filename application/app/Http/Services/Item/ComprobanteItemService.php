<?php

namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Models\ComprobanteItem;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\BussinessException;


class ComprobanteItemService {

    public function generarUrlTemporaria(array $request, int $usuarioId){

        beginTransaction();
        try {

            $storage = Storage::temporaryUploadUrl(
                $request["nombre_archivo"], now()->addMinutes(5)
            );
            
            $comprobante = ComprobanteItem::create([
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

    public function updateComprobante(ComprobanteItem $comprobanteItem, array $request) :ComprobanteItem {

        beginTransaction();
        try {

            $comprobanteItem->path = $request["path"];
            $comprobanteItem->save();

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::COMPROBANTE_ITEM_ACTUALIZAR_ERROR_MESSAGE, AppErrors::COMPROBANTE_ITEM_ACTUALIZAR_ERROR_CODE);
        }
        
        commit();
        return $comprobanteItem;

    }

}