<?php

namespace App\Http\Controllers;

use App\Models\ItemTipo;

class ItemTipoController extends Controller
{
    public function findAll(){
        return response()->json(ItemTipo::get());
    }
}
