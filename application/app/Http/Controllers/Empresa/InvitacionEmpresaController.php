<?php

namespace App\Http\Controllers\Empresa;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvitacionEmpresa\GetInvitacionRequest;
use App\Http\Requests\InvitacionEmpresa\SaveInvitacionRequest;
use App\Http\Services\EmailService;
use App\Http\Services\Empresa\InvitacionEmpresaService;
use App\Models\Empresa;
use App\Models\InvitacionEmpresa;
use App\Models\InvitacionEstado;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use Illuminate\Http\Request;

class InvitacionEmpresaController extends Controller
{

    public function __construct(
        public InvitacionEmpresaService $invitacionEmpresaService 
    )
    {}

    public function findAll(GetInvitacionRequest $request){
        $data = $request->all();
        $invitaciones = $this->invitacionEmpresaService->findAll($data);
        return response()->json($invitaciones);
        
    }

    public function create(SaveInvitacionRequest $request){


        try {

            $data = $request->all();
            $emailInvitado = $data["email_invitado"];
            $empresaId = $data["empresa_id"];
            $invitadorId = $data["invitador_id"];
            $empresaId = $data["empresa_id"];

            if(!UsuarioEmpresa::where("usuario_id", $invitadorId)->exists()){
                throw new BussinessException(AppErrors::EMPRESA_USER_NOT_EXISTS_MESSAGE, AppErrors::EMPRESA_USER_NOT_EXISTS_CODE);
            }

            if(InvitacionEmpresa::where("email_invitado", $emailInvitado)
                                ->where("empresa_id", $empresaId)
                                ->whereIn("invitacion_estado_id", [InvitacionEstado::INVITADO, InvitacionEstado::ACEPTADO])
                                ->exists()){
                throw new BussinessException(AppErrors::INVITACION_DUPLICADA_MESSAGE, AppErrors::INVITACION_DUPLICADA_CODE);
            }

            $invitacion = $this->invitacionEmpresaService->create($data);

            $emailService = new EmailService();

            $empresa = Empresa::find($empresaId);
            $existeUsuario = User::where("email", $emailInvitado)->exists();
            $parametros = [
                "urlDescarga" => "https://play.google.com/store/apps/details?id=ruteador.flex.app",
                "urlApp" => "ruteador://",
                "empresa" => $empresa
            ];

            $template = $existeUsuario 
                        ? view('Emails.InvitacionEmpresa.UsuarioExistente', $parametros) 
                        : view('Emails.InvitacionEmpresa.NuevoUsuario', $parametros);

            $configEmail = config('app.values');

            $emailService->sendEmail(
                $configEmail["MAIL_HOST"],
                $configEmail["MAIL_USERNAME"],
                $configEmail["MAIL_PASSWORD"],
                $configEmail["MAIL_USERNAME"],
                "Invitacion $empresa->nombre",
                $emailInvitado,
                'Socio',
                $template,
            );

            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        return $invitacion;
        
       
    }

    public function aceptarInvitacion(InvitacionEmpresa $invitacionEmpresa, Request $request) {

        try {   
            $usuario = $request->user();
            if($usuario->email !== $invitacionEmpresa->email_invitado){
                throw new BussinessException(AppErrors::INVITACION_NO_PERTENECE_USUARIO_MESSAGE, AppErrors::INVITACION_NO_PERTENECE_USUARIO_CODE);
            }
            
            $existeUsuarioEmpresa = UsuarioEmpresa::where("usuario_id", $usuario->id)
                                                    ->whereNull("deleted_at")
                                                    ->withTrashed()
                                                    ->exists();
            if($existeUsuarioEmpresa){
                throw new BussinessException(AppErrors::INVITACION_DUPLICADA_MESSAGE, AppErrors::INVITACION_DUPLICADA_CODE);
            }
           
            $data = [
                "usuario_id"    => $usuario->id,
                "empresa_id"    => $invitacionEmpresa->empresa_id,
                "rol_id"        => $invitacionEmpresa->rol_id,
                "invitacion_id" => $invitacionEmpresa->id
            ];

            $invitacion = $this->invitacionEmpresaService->aceptarInvitacion($invitacionEmpresa, $data);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        return response()->json($invitacion);
        
    }

    public function rechazarInvitacion(InvitacionEmpresa $invitacionEmpresa, Request $request) {
        try {   

            $usuario = $request->user();
            if($usuario->email !== $invitacionEmpresa->email_invitado){
                throw new BussinessException(AppErrors::INVITACION_NO_PERTENECE_USUARIO_MESSAGE, AppErrors::INVITACION_NO_PERTENECE_USUARIO_CODE);
            }

            $invitacion = $this->invitacionEmpresaService->rechazarInvitacion($invitacionEmpresa);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        return response()->json($invitacion);
    }

    public function eliminarInvitacion(InvitacionEmpresa $invitacionEmpresa, Request $request) {
        try {   
            $invitacion = $this->invitacionEmpresaService->eliminarInvitacion($invitacionEmpresa);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        return response()->json($invitacion);
    }

}
