<?php


namespace App\Http\Services\Empresa;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Empresa;
use App\Models\UsuarioEmpresa;
use Illuminate\Database\Eloquent\Builder;

class EmpresaService {

    public function usuarioPerteneceEmpresa(int $usuarioId, int $empresaId){

        return UsuarioEmpresa::where("usuario_id", $usuarioId)->where('empresa_id', $empresaId)->exists();
    }

    public function findAll(array $parametros){
        $query = Empresa::query();

        
        $query = $query
                    
                    ->when(isset($parametros["incluir"]), function(Builder $q) use($parametros) {
                        $q->with($parametros["incluir"]);
                    })
                    ->when(isset($parametros["empresa_id"]), function(Builder $q) use($parametros) {
                        $q->whereIn("id", $parametros["empresa_id"]);
                    })
                    ->when(isset($parametros["rol_id"]), function(Builder $q) use($parametros) {
                        $q->whereHas('usuariosEmpresas', function (Builder $query) use($parametros) {
                            $query->where('rol_id', $parametros["rol_id"]);
                            if(isset($parametros["usuario_empresa_id"])){
                                $query->where('usuario_id', $parametros["usuario_empresa_id"]);
                            }
                        });
                    });
 
        return isset($parametros["page"]) ?  $query->paginate() : $query->get();
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