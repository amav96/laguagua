<?php

namespace App\Http\Controllers;

use App\Models\EstadoItem;
use Illuminate\Http\Request;

class EstadoItemController extends Controller
{
    public function findAll(){
        return response()->json(EstadoItem::get());
    }
}
