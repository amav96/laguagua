<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function findAll(Request $request){
        $usuario = $request->user();
         
        $roles = Rol::when($usuario->rol_id !== Rol::ADMINISTRADOR_SISTEMA, function($query){
            $query->whereNotIn("id", [Rol::ADMINISTRADOR_SISTEMA, Rol::VENDEDOR]);
        })
        ->get();
        
        return $roles;
    }
}
