<?php

namespace App\Http\Controllers;

use App\Models\ProveedorItem;
use Illuminate\Http\Request;

class ProveedorItemController extends Controller
{
    public function findAll(){
        return response()->json(ProveedorItem::get());
    }
}
