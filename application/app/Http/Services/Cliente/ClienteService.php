<?php

namespace App\Http\Services\Cliente;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Cliente;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ClienteService {

    public function findAll(array $filtros) {

        $query = Cliente::query();
        
        $query = $query
                ->when(isset($filtros["cliente_id"]), function (Builder $q) use($filtros) : void {
                    $q->where('id', $filtros["cliente_id"]); 
                })
                ->when(isset($filtros["tipo_documento_id"]), function (Builder $q) use($filtros) : void {
                    $q->where('tipo_documento_id', $filtros["tipo_documento_id"]); 
                })
                ->when(isset($filtros["numero_documento"]), function (Builder $q) use($filtros) : void {
                    $q->where('numero_documento', 'LIKE', "%".$filtros["numero_documento"]."%"); 
                });

        if(isset($filtros["page"])){
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
                "codigo_area_id" => $request["codigo_area_id"] ?? null,
                "numero_celular" => isset($request["numero_celular"]) ? trim($request["numero_celular"]) : null,
                "numero_fijo" => isset($request["numero_fijo"]) ? trim($request["numero_fijo"]) : null,
                "empresa_id" => $request["empresa_id"] ?? null,
                "creado_por" => $usuarioId
            ]);

            $cliente = Cliente::create($clienteData);

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::CLIENTE_CREAR_ERROR_MESSAGE, AppErrors::CLIENTE_CREAR_ERROR_CODE);
        }

        commit();
        return $cliente;

    }
    
    public function update(Cliente $cliente,  array $request){

        beginTransaction();
        try {
            
            $cliente->fill([
                'tipo_documento_id' => $request['tipo_documento_id'] ?? $cliente->tipo_documento_id,
                'numero_documento'  => $request['numero_documento'] ?? $cliente->numero_documento,
                'nombre'            => $request['nombre'] ?? $cliente->nombre,
                'codigo_area_id'    => $request['codigo_area_id'] ?? $cliente->codigo_area_id,
                'numero_celular'    => $request['numero_celular'] ?? $cliente->numero_celular,
                'numero_fijo'       => $request['numero_fijo'] ?? $cliente->numero_fijo,
                'empresa_id'        => $request['empresa_id'] ?? $cliente->empresa_id,
            ]);
    
            $cliente->save();
            
        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::CLIENTE_ACTUALIZAR_ERROR_MESSAGE, AppErrors::CLIENTE_ACTUALIZAR_ERROR_CODE);
        }

        commit();
        return $cliente;

    }
}