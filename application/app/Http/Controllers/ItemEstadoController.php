<?php

namespace App\Http\Controllers;

use App\Models\ItemEstado;
use Illuminate\Http\Request;

class ItemEstadoController extends Controller
{
    public function findAll(){
        return response()->json(ItemEstado::get());
    }
}
