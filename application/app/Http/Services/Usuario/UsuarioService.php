<?php
namespace App\Http\Services\Usuario;

use App\Config\Seguridad\ValuePermiso;
use App\Models\User;

class UsuarioService {

    public function permisos(int $usuarioId){
        $usuario = User::find($usuarioId);
        
        if($usuario->email !== "alvaroamav96@gmail.com"){
            return [];
        }

        return collect(ValuePermiso::rolesPermisos())
        ->filter(fn($grupo) => $grupo["administrador"] === true)
        ->map(fn($grupo) => $grupo["nombre"])
        ->toArray();
    }
}