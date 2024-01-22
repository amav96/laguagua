<?php

namespace App\Http\Controllers;

use App\Models\ParadaEstado;
use Illuminate\Http\Request;

class EstadoParadaController extends Controller
{
    public function findAll(){
        return response()->json(ParadaEstado::get());
    }
}
