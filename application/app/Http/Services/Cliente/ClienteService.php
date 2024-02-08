<?php

namespace App\Http\Services\Cliente;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Cliente;
use App\Models\ClienteNumero;
use Illuminate\Database\Eloquent\Builder;

class ClienteService {

    public function findAll(array $params) {

        $query = Cliente::query();
      
        $query = $query
                ->when(isset($params["incluir"]), function (Builder $q) use($params) : void {
                    $q->with($params["incluir"]); 
                })
                ->when(isset($params["cliente_id"]), function (Builder $q) use($params) : void {
                    $q->where('id', $params["cliente_id"]); 
                })
                ->when(isset($params["tipo_documento_id"]), function (Builder $q) use($params) : void {
                    $q->where('tipo_documento_id', $params["tipo_documento_id"]); 
                })
                ->when(isset($params["numero_documento"]), function (Builder $q) use($params) : void {
                    $q->where('numero_documento', 'LIKE', "%".$params["numero_documento"]."%"); 
                });

        if(isset($params["page"])){
            $query = $query->paginate();
        } else {
            $query = $query->get();
        }

        return $query;

    }

    public function create(array $request, int $usuarioId){

        beginTransaction();
        try {

            $clienteData = array_filter([
                "tipo_documento_id" => $request["tipo_documento_id"] ?? null,
                "numero_documento" => isset($request["numero_documento"]) ? trim($request["numero_documento"]) : null,
                "nombre" => isset($request["nombre"]) ? trim($request["nombre"]) : null,
                "empresa_id" => $request["empresa_id"] ?? null,
                "observaciones" => $request["observaciones"] ?? null,
                "creado_por" => $usuarioId
            ]);

            $cliente = Cliente::create($clienteData);

            if(isset($request["clientes_numeros"])){
                foreach($request["clientes_numeros"] as $contacto){
                    ClienteNumero::create([
                        "cliente_id"        => $cliente->id,
                        "codigo_area_id"    => $contacto["codigo_area_id"],
                        "numero"            => $contacto["numero"],
                    ]);
                }
            }

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::CLIENTE_CREAR_ERROR_MESSAGE, AppErrors::CLIENTE_CREAR_ERROR_CODE);
        }

        commit();
        return $cliente->load('clientesNumeros');

    }
    
    public function update(Cliente $cliente,  array $request){

        beginTransaction();
        try {
            
            $cliente->fill([
                "tipo_documento_id" => $request["tipo_documento_id"] ?? $cliente->tipo_documento_id,
                "numero_documento"  => $request["numero_documento"] ?? $cliente->numero_documento,
                "nombre"            => $request["nombre"] ?? $cliente->nombre,
                "observaciones"     => $request["observaciones"] ?? $cliente->observaciones,
                "empresa_id"        => $request["empresa_id"] ?? null,
            ]);
    
            $cliente->save();

           
            if (isset($request["clientes_numeros"])) {
                foreach ($request["clientes_numeros"] as $contacto) {
                    if (isset($contacto["id"])) {
                        // Si hay un ID, actualiza el registro existente
                        $clienteNumero = ClienteNumero::findOrFail($contacto["id"]);
                        $clienteNumero->update([
                            "codigo_area_id" => $contacto["codigo_area_id"],
                            "numero" => $contacto["numero"],
                        ]);
                    } else {
                        // Si no hay un ID, crea un nuevo registro
                        $cliente->clientesNumeros()->create([
                            "codigo_area_id" => $contacto["codigo_area_id"],
                            "numero" => $contacto["numero"],
                        ]);
                    }
                }
            }
            
        } catch (\Throwable $th) {
            rollBack();

            throw new BussinessException(AppErrors::CLIENTE_ACTUALIZAR_ERROR_MESSAGE, AppErrors::CLIENTE_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        return $cliente->load('clientesNumeros');

    }
}