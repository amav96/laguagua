<?php

namespace App\Http\Controllers;

use App\Models\TipoParada;
use Illuminate\Http\Request;

class TipoItemController extends Controller
{
    public function findAll(){
        return response()->json(TipoParada::get());
    }
}
