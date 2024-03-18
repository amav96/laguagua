<?php

namespace App\Http\Services\Empresa;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\InvitacionEmpresa;
use App\Models\InvitacionEstado;
use App\Models\UsuarioEmpresa;
use Illuminate\Database\Eloquent\Builder;

class InvitacionEmpresaService {

    public function findAll(array $parametros){
        $query = InvitacionEmpresa::query();

        $query = $query
                ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                    $q->with($parametros["incluir"]);
                })
                ->when(isset($parametros["email_invitado"]) && isset($parametros["invitador_id"]) , function($q) use($parametros) {
                    $q->where(function($b) use($parametros){
                        $b->where("email_invitado", $parametros["email_invitado"])
                        ->orWhere("invitador_id", $parametros["invitador_id"]);
                    });
                })
                ->when(isset($parametros["email_invitado"]) && !isset($parametros["invitador_id"]), function($q) use($parametros) {
                    $q->where("email_invitado", $parametros["email_invitado"]);
                })
                ->when(isset($parametros["invitador_id"]) && !isset($parametros["email_invitado"]), function($q) use($parametros) {
                    $q->where("invitador_id", $parametros["invitador_id"]);
                })
                ->orderBy("created_at", "desc");

        return isset($parametros["page"]) ?  $query->paginate() : $query->get();

    }


    public function create(array $request): InvitacionEmpresa {

        $invitacion = new InvitacionEmpresa();
        $invitacion->email_invitado = $request["email_invitado"];
        $invitacion->invitador_id = $request["invitador_id"];
        $invitacion->rol_id = $request["rol_id"];
        $invitacion->empresa_id = $request["empresa_id"];
        $invitacion->invitacion_estado_id = InvitacionEstado::INVITADO;
        $invitacion->save();

        return $invitacion;
    }

    public function aceptarInvitacion(InvitacionEmpresa $invitacionEmpresa, array $request) : InvitacionEmpresa {

        try {

            UsuarioEmpresa::create([
                "usuario_id" => $request["usuario_id"],
                "empresa_id" => $request["empresa_id"],
                "rol_id"     => $request["rol_id"],
                "invitacion_id" => $request["invitacion_id"]
            ]);

            $invitacionEmpresa->invitacion_estado_id = InvitacionEstado::ACEPTADO;
            $invitacionEmpresa->save();

        } catch (\Throwable $th) {
            rollBack();
            throw new BussinessException(AppErrors::INVITACION_ERROR_AL_ACEPTAR_MESSAGE, AppErrors::INVITACION_ERROR_AL_ACEPTAR_CODE);
        }

        commit();

        return $invitacionEmpresa->load(['rol', 'empresa', 'invitador', 'estado']);

    }

    public function rechazarInvitacion(InvitacionEmpresa $invitacionEmpresa) : InvitacionEmpresa {

        $invitacionEmpresa->invitacion_estado_id = InvitacionEstado::RECHAZADO;
        $invitacionEmpresa->save();

        return $invitacionEmpresa->load(['rol', 'empresa', 'invitador', 'estado']);
    }

    public function eliminarInvitacion(InvitacionEmpresa $invitacionEmpresa) : InvitacionEmpresa {

        $invitacionEmpresa->deleted_at = now();
        $invitacionEmpresa->save();

        return $invitacionEmpresa->load(['rol', 'empresa', 'invitador', 'estado']);
    }

    public function terminarInvitacion(InvitacionEmpresa $invitacionEmpresa) : InvitacionEmpresa {

        $invitacionEmpresa->invitacion_estado_id = InvitacionEstado::TERMINADO;
        $invitacionEmpresa->save();

        return $invitacionEmpresa->load(['rol', 'empresa', 'invitador', 'estado']);
    }

    
}

