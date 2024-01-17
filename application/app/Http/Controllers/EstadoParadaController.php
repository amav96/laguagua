<?php

namespace App\Http\Controllers;

use App\Models\EstadoParada;
use Illuminate\Http\Request;

class EstadoParadaController extends Controller
{
    public function findAll(){
        return response()->json(EstadoParada::get());
    }
}
