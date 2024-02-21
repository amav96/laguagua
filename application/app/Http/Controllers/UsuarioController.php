<?php

namespace App\Http\Controllers;

use App\Config\Seguridad\ValuePermiso;
use App\Http\Requests\Usuario\FindAllUsuarioRequest;
use App\Http\Services\Usuario\UsuarioService;

class UsuarioController extends Controller
{

    public function __construct(
        public UsuarioService $usuarioService
    ){}

    public function findAll(FindAllUsuarioRequest $request){
        
        $usuario = $request->user()->load("pais");

        if((!$request->usuario_id) || ($request->usuario_id !== $usuario->id)){
            autorizado($usuario, ValuePermiso::ADMINISTRACION_USUARIOS_LISTADO);
        }

        $parametros = $request->all();

        if(isset($request->usuario_id)){
            $parametros["usuario_id"] = $request->usuario_id;
        }
        
        $parametros["time_zone"] = $usuario->pais->time_zone;
        
        $usuarios = $this->usuarioService->findAll($parametros);
       
        return response()->json($usuarios);
    }
    
}
