<?php

namespace App\Http\Controllers\Recorrido;

use App\Http\Controllers\Controller;
use App\Models\RecorridoEstado;

class RecorridoEstadoController extends Controller
{
    public function findAll(){
        return response()->json(RecorridoEstado::get());
    }
}
