<?php

namespace App\Http\Controllers;

use App\Config\Seguridad\ValuePermiso;
use App\Http\Requests\Usuario\FindAllUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Http\Services\Usuario\UsuarioService;
use App\Models\User;

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

    public function update(User $usuario, UpdateUsuarioRequest $request){

        $usuarioAutenticado = $request->user();
        if($usuario->id !== $usuarioAutenticado->id){
            autorizado($usuarioAutenticado, ValuePermiso::ADMINISTRACION_USUARIOS_ACTUALIZAR_DIFERENTE);
        }

        $usuarioActualizado = $this->usuarioService->update($usuario, $request->all());
        
        return response()->json([
            'nombre' => $usuarioActualizado->nombre,
            'pais_id' => $usuarioActualizado->pais_id,
            'pais' => $usuarioActualizado->pais,
        ]);
    }
    
}
