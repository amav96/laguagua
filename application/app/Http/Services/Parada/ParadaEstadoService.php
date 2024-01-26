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

    public function paradaVisitada(int $estadoParadaId) :bool {
        $visitada = false;
        switch($estadoParadaId){
            case ParadaEstado::VISITADO;
            $visitada = true;
            break;
            case ParadaEstado::CANCELADO;
            $visitada = true;
            break;
            case ParadaEstado::NO_RESPONDE;
            $visitada = true;
            break;
            case ParadaEstado::DIRECCION_INCORRECTA;
            $visitada = true;
            break;
            case ParadaEstado::FALTAN_DATOS;
            $visitada = true;
            break;
            case ParadaEstado::RECHAZADO;
            $visitada = true;
            break;
        }

        return $visitada;

    }
    
}