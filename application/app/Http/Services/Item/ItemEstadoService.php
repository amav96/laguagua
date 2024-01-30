<?php


namespace App\Http\Services\Item;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\ItemEstado;
use Illuminate\Database\Eloquent\Builder;

class ItemEstadoService {
    
    public function findAll(array $parametros) {

        $query = ItemEstado::query();
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
