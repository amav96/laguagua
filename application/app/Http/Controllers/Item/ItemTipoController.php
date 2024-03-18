<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\ItemTipo;

class ItemTipoController extends Controller
{
    public function findAll(){
        return response()->json(ItemTipo::get());
    }
}
