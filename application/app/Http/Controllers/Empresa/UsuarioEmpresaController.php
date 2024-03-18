<?php

namespace App\Http\Controllers\Empresa;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Empresa\GetUsuarioEmpresaRequest;
use App\Http\Services\Empresa\InvitacionEmpresaService;
use App\Http\Services\Empresa\UsuarioEmpresaService;
use App\Models\InvitacionEmpresa;
use App\Models\Rol;
use App\Models\UsuarioEmpresa;
use Illuminate\Http\Request;

class UsuarioEmpresaController extends Controller
{
    public function __construct(
        public UsuarioEmpresaService $usuarioEmpresaService
    ){}

    public function findAll(GetUsuarioEmpresaRequest $request){

        try {   
            $usuario = $request->user();

            if(!isset($request->usuario_id) && $request->empresa_id){
                if(!$this->usuarioEmpresaService->usuarioEsAdminEmpresa($usuario->id, $request->empresa_id)){
                    throw new BussinessException(AppErrors::USUARIO_EMPRESA_NO_PERMITIDO_MESSAGE, AppErrors::USUARIO_EMPRESA_NO_PERMITIDO_CODE);
                }
            }
            
            if(isset($request->usuario_id) && $usuario->id !== (int)$request->usuario_id){
                throw new BussinessException(AppErrors::USUARIO_EMPRESA_NO_PERTENECE_MESSAGE, AppErrors::USUARIO_EMPRESA_NO_PERTENECE_CODE);
            }
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        $usuariosEmpresas = $this->usuarioEmpresaService->findAll($request->all());

        return response()->json($usuariosEmpresas);
    }

    public function terminarRelacion(UsuarioEmpresa $usuarioEmpresa, Request $request){

        try {   
            $usuario = $request->user();
            
            if($usuario->id !== $usuarioEmpresa->usuario_id){
                if(!$this->usuarioEmpresaService->usuarioEsAdminEmpresa($usuario->id, $usuarioEmpresa->empresa_id)){
                    throw new BussinessException(AppErrors::USUARIO_EMPRESA_NO_PERMITIDO_MESSAGE, AppErrors::USUARIO_EMPRESA_NO_PERMITIDO_CODE);
                }
            }
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }

        $usuarioEmpresa = $this->usuarioEmpresaService->terminarRelacion($usuarioEmpresa);

        $invitacionEmpresaService =  new InvitacionEmpresaService();
        $invitacionEmpresaService->terminarInvitacion(InvitacionEmpresa::find($usuarioEmpresa->invitacion_id));
        
        return response()->json($usuarioEmpresa);

    }
}
