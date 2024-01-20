<?php

namespace App\Http\Controllers;

use App\Models\TipoItem;
use Illuminate\Http\Request;

class TipoItemController extends Controller
{
    public function findAll(){
        return response()->json(TipoItem::get());
    }
}
