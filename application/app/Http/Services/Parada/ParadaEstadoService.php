<?php

namespace App\Http\Services\Parada;

use App\Models\ParadaEstado;
use Illuminate\Database\Eloquent\Builder;

class ParadaEstadoService {

    public function findAll(array $parametros) {

        $query = ParadaEstado::query();
        $query = $query
                ->when(isset($parametros["tipo"]), function (Builder $q) use($parametros) : void {
                    $q->where('tipo', $parametros["tipo"]); 
                });

        if(isset($parametros["page"])){
            $query = $query->paginate();
        } else {
            $query = $query->get();
        }

        return $query;

    }
    
}