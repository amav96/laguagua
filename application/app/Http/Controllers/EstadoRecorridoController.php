<?php

namespace App\Http\Controllers;

use App\Models\EstadoRecorrido;
use Illuminate\Http\Request;

class EstadoRecorridoController extends Controller
{
    public function findAll(){
        return response()->json(EstadoRecorrido::get());
    }
}
