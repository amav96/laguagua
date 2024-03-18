<?php


namespace App\Http\Services\Empresa;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\Empresa;
use App\Models\Rol;
use App\Models\UsuarioEmpresa;
use Illuminate\Database\Eloquent\Builder;

class UsuarioEmpresaService {

    public function findAll(array $parametros){

        $query = UsuarioEmpresa::query();

        $query = $query
                ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                    $q->with($parametros["incluir"]);
                })
                ->when(isset($parametros["usuario_id"]), function(Builder $q) use($parametros) {
                    $q->where("usuario_id", $parametros["usuario_id"]);
                })
                ->when(isset($parametros["empresa_id"]), function(Builder $q) use($parametros) {
                    $q->where("empresa_id", $parametros["empresa_id"]);
                })
                ->when(isset($parametros["rol_id"]), function(Builder $q) use($parametros) {
                    $q->where("rol_id", $parametros["rol_id"]);
                })
                ->when(isset($parametros["eliminadas"]), function(Builder $q) use($parametros) {
                    $q->withTrashed();
                })
                ->orderBy("created_at", "desc")
                ->orderBy("deleted_at", "desc");

        return isset($parametros["page"]) ?  $query->paginate() : $query->get();
    }

    public function terminarRelacion(UsuarioEmpresa $usuarioEmpresa) : UsuarioEmpresa {
        $usuarioEmpresa->deleted_at = now();
        $usuarioEmpresa->save();
        return $usuarioEmpresa->load(["rol","empresa","invitacion.invitador","usuario"]);
    }

    public function usuarioEsAdminEmpresa(int $usuarioId, int $empresaId) : bool{
        
        return UsuarioEmpresa::where("usuario_id", $usuarioId)
        ->where("empresa_id", $empresaId)
        ->where("rol_id", Rol::ADMINISRTADOR_AGENCIA)->exists();
    }

}