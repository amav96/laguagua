<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaisController extends Controller
{
    public function findAll(){
        $paises = DB::table("paises")
                    ->select([
                        "paises.id",
                        "paises.nombre",
                        "paises.iso",
                        "codigos_area.bandera"
                    ])
                    ->join('codigos_area', 'paises.iso', '=', 'codigos_area.iso')
                    ->get();
                    
        return response()->json($paises);
    }
}
