<?php

namespace App\Http\Controllers;

use App\Models\RecorridoEstado;
use Illuminate\Http\Request;

class RecorridoEstadoController extends Controller
{
    public function findAll(){
        return response()->json(RecorridoEstado::get());
    }
}
