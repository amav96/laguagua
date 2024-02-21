<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function findAll(Request $request){
        
        $usuario = $request->user()->load("pais");
        autorizado($usuario);
        
        // TODO: poner timezone dinamico por usuario admin
        $fechaArgentina = now()->setTimezone($usuario->pais->time_zone)->toDateString();
        $usuarios = User::with(["usuarioConsumo"])
                    ->withCount(['paradas as paradas_hoy' => function ($query) use($fechaArgentina) {
                        $query->whereDate('created_at', $fechaArgentina)
                            ->whereColumn('rider_id', 'usuarios.id')
                            ->select(DB::raw('COUNT(*)'));
                    }])
                    ->withCount(['paradas as paradas_total' => function ($query) {
                        $query->whereColumn('rider_id', 'usuarios.id')
                            ->select(DB::raw('COUNT(*)'));
                    }])
                    ->paginate();
        return response()->json($usuarios);
    }
    
}
