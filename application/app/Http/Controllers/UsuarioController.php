<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsuarioConsumo;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function findAll(Request $request){
        
        autorizado($request->user());

        $usuarios = User::with(["usuarioConsumo"])->paginate();
        return response()->json($usuarios);
    }
}
