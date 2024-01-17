<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function findAll(){
        return response()->json(TipoDocumento::get());
    }
}
