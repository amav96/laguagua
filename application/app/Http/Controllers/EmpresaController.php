<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\UsuarioEmpresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function findAll(Request $request){
        
        $usuario = $request->user();
        $usuarioEmpresas = UsuarioEmpresa::where('usuario_id', $usuario->id)->get();
        return response()->json(Empresa::whereIn('id', $usuarioEmpresas->pluck("empresa_id"))->get());
    }
}
