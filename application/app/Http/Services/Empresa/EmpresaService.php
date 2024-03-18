<?php


namespace App\Http\Services\Empresa;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Empresa;
use App\Models\UsuarioEmpresa;

class EmpresaService {

    public function usuarioPerteneceEmpresa(int $usuarioId, int $empresaId){

        return UsuarioEmpresa::where("usuario_id", $usuarioId)->where('empresa_id', $empresaId)->exists();
    }

    public function create(array $request): Empresa{

        $empresa = new Empresa();
        $empresa->nombre = $request["nombre"];
        $empresa->usuario_id = $request["usuario_id"];
        $empresa->save();

        $usuarioEmpresa = new  UsuarioEmpresa();
        $usuarioEmpresa->usuario_id = $empresa->usuario_id;
        $usuarioEmpresa->empresa_id = $empresa->id;
        $usuarioEmpresa->save();

        return $empresa;
    }

    public function update(Empresa $empresa, array $request) : Empresa{

        beginTransaction();
        try {

            $empresa->fill([
                "nombre" => $request["nombre"],
            ]);
    
            $empresa->save();

        } catch (\Throwable $th) {
            rollBack();
            
            throw new BussinessException(AppErrors::EMPRESA_UPDATE_MESSAGE, AppErrors::EMPRESA_UPDATE_CODE);
        }

        commit();
        return $empresa;

    }

    public function delete(Empresa $empresa) : Empresa{

        beginTransaction();
        try {

            $empresa->deleted_at = now();
            $empresa->save();

        } catch (\Throwable $th) {
            rollBack();
            
            throw new BussinessException(AppErrors::EMPRESA_UPDATE_MESSAGE, AppErrors::EMPRESA_UPDATE_CODE);
        }

        commit();

        return $empresa;

    }
}