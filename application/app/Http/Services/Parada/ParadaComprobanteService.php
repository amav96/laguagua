<?php

namespace App\Http\Services\Parada;

use App\Exceptions\AppErrors;
use App\Models\ParadaComprobante;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\BussinessException;


class ParadaComprobanteService {

    public function generarUrlTemporaria(array $request, int $usuarioId){

        beginTransaction();
        try {

            $path = 'paradas-comprobantes/'.$request["nombre_archivo"];

            $storage = Storage::temporaryUploadUrl(
                $path, now()->addMinutes(180)
            );
            
            $comprobante = ParadaComprobante::create([
                "parada_id"       => $request["parada_id"],
                "usuario_id"    => $usuarioId,
                "path"          => $path
            ]);


        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::COMPROBANTE_PARADA_CREAR_ERROR_MESSAGE, AppErrors::COMPROBANTE_PARADA_CREAR_ERROR_CODE);
        }
        
        commit();

    
        return [
            "comprobante" => $comprobante,
            "storage" => $storage
        ];
    }

    public function delete (ParadaComprobante $paradaComprobante){
       try {

            $paradaComprobante->delete();
           
       } catch (\Throwable $th) {
            throw new BussinessException(AppErrors::COMPROBANTE_PARADA_ELIMINAR_ERROR_MESSAGE, AppErrors::COMPROBANTE_PARADA_ELIMINAR_ERROR_CODE);
       }

       return $paradaComprobante;
    }

}