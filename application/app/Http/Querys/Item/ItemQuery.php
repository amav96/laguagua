<?php
namespace App\Http\Querys\Item;

use App\Models\Item;
use App\Models\ItemEstado;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ItemQuery {
    
    public function estadoItemQuery(array $parametros){

      
        return Item::selectRaw('
                    SUM(CASE WHEN item_estado_id = ' . ItemEstado::ENTREGADO . ' THEN 1 ELSE 0 END) as entregados,
                    SUM(CASE WHEN item_estado_id = ' . ItemEstado::RETIRADO . ' THEN 1 ELSE 0 END) as retirados,
                    SUM(CASE WHEN item_estado_id = ' . ItemEstado::PREPARADO . ' THEN 1 ELSE 0 END) as preparados,
                    SUM(CASE WHEN item_estado_id != ' . ItemEstado::ENTREGADO . ' AND item_estado_id != ' . ItemEstado::PREPARADO . ' THEN 1 ELSE 0 END) as no_entregados
                ')
                ->when(isset($parametros["fecha_inicio"]), function (Builder $q) use($parametros) : void {
                    $fechaInicio = Carbon::parse($parametros["fecha_inicio"])->format('Y-m-d');
                    $q->whereRaw("DATE(CONVERT_TZ(created_at, 'UTC', '{$parametros["time_zone"]}')) >= ?", [$fechaInicio]); 
                })
                ->when(isset($parametros["fecha_fin"]), function (Builder $q) use($parametros) : void {
                    $fechaFin = Carbon::parse($parametros["fecha_fin"])->format('Y-m-d');
                    $q->whereRaw("DATE(CONVERT_TZ(created_at, 'UTC', '{$parametros["time_zone"]}')) <= ?", [$fechaFin]); 
                })
                ->when(isset($parametros["empresa_id"]), function (Builder $q) use($parametros) : void {
                    $q->where('empresa_id', $parametros["empresa_id"]); 
                })
                ->when(isset($parametros["creado_por"]), function (Builder $q) use($parametros) : void {
                    $q->where('creado_por', $parametros["creado_por"]); 
                });
    }

    public function findAll(array $parametros){

        $query = Item::query();
        $timeZone = $parametros["time_zone"];
        return $query
            ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                $q->with($parametros["incluir"]);
            })
            ->when(isset($parametros["item_id"]), function (Builder $q) use($parametros) : void {
                $q->where('id', $parametros["item_id"]); 
            })
            ->when(isset($parametros["empresa_id"]), function (Builder $q) use($parametros) : void {
                $q->where('empresa_id', $parametros["empresa_id"]); 
            })
            ->when(isset($parametros["fecha_inicio"]), function (Builder $q) use($parametros, $timeZone) : void {
                $fechaInicio = Carbon::parse($parametros["fecha_inicio"])->format('Y-m-d');
                $q->whereRaw("DATE(CONVERT_TZ(created_at, 'UTC', '{$timeZone}')) >= ?", [$fechaInicio]); 
            })
            ->when(isset($parametros["fecha_fin"]), function (Builder $q) use($parametros, $timeZone) : void {
                $fechaFin = Carbon::parse($parametros["fecha_fin"])->format('Y-m-d');
                $q->whereRaw("DATE(CONVERT_TZ(created_at, 'UTC', '{$timeZone}')) <= ?", [$fechaFin]); 
            })
            ->when(isset($parametros["creado_por"]), function (Builder $q) use($parametros) : void {
                $q->where('creado_por', $parametros["creado_por"]); 
            })
            // ->when(isset($parametros["busqueda"]), function (Builder $q) use($parametros) : void {
            //     $q->join('paradas', 'items.parada_id', '=', 'paradas.id')
            //     // ->whereRaw("LOWER(paradas.direccion_formateada) COLLATE utf8mb4_general_ci LIKE LOWER(?) COLLATE utf8mb4_general_ci", ['%' . $parametros["busqueda"] . '%']);
            //     ->where('paradas.direccion_formateada', 'LIKE', '%' . $parametros["busqueda"] . '%')
            //     ->orWhere('paradas.localidad', 'LIKE', '%' . $parametros["busqueda"] . '%');
            // })
            ->when(isset($parametros["busqueda"]), function (Builder $q) use($parametros) : void {
                $q->whereHas('parada', function (Builder $query) use($parametros) {
                    $query->where('direccion_formateada', 'LIKE', '%' . $parametros["busqueda"] . '%')
                            ->orWhere('localidad', 'LIKE', '%' . $parametros["busqueda"] . '%');
                })->orWhere('track_id', 'LIKE', '%' . $parametros["busqueda"] . '%');
            })
            ->orderByRaw('ISNULL(gestionado), gestionado DESC, created_at DESC');
    }
}