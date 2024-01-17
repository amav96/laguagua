<?php

namespace App\Http\Controllers;

use App\Models\CodigoArea;
use Illuminate\Http\Request;

class CodigoAreaController extends Controller
{
    public function findAll(){
        return response()->json(CodigoArea::get());
    }
}
