<?php

namespace App\Http\Controllers;

use App\Models\ItemProveedor;
use Illuminate\Http\Request;

class ProveedorItemController extends Controller
{
    public function findAll(){
        return response()->json(ItemProveedor::get());
    }
}
