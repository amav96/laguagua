<?php

namespace App\Http\Controllers\Informes\Item;

use App\Config\Seguridad\ValuePermiso;
use App\Exceptions\BussinessException;
use App\Exports\Item\ReporteItemGestionExport;
use App\Http\Querys\Item\ItemQuery;
use App\Http\Requests\Item\InformeExcelItemRequest;
use App\Http\Services\Item\ItemService;
use App\Http\Controllers\Controller;
use App\Http\Services\ConsumoService;
use App\Http\Services\EmailService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class InformeItemGestionController extends Controller
{
    public function __construct(
        public ItemService $itemService
    ){}

    private function constructor(InformeExcelItemRequest $request){
        $parametros = $request->all();
       
        $usuarioAutenticado = $request->user();
        if($usuarioAutenticado->id !== $request->creado_por){
            autorizado($request->user(), ValuePermiso::ADMINISTRACION_INFORMES_USUARIOS);
        }
        $usuario = User::find($request->creado_por)->load("pais");
        $parametros["creado_por"] = $usuario->id;
        $parametros["time_zone"] = $usuario->pais->time_zone;
    
        $items = $this->itemService->findAll($parametros);
    
        $estadosQuery = new ItemQuery;
        $estados = $estadosQuery->estadoItemQuery($parametros)->get()->first();
       
        $totalItems = method_exists($items, 'total') ? $items->total() : count($items);
        
        return [
            "metricas" => [
                "entregados" => $estados->entregados,
                "entregados_porcentaje" => $totalItems === 0 ? 0 : round(($estados->entregados / $totalItems) * 100, 2),
                "retirados" => $estados->retirados,
                "retirados_porcentaje" => $totalItems === 0 ? 0 : round(($estados->retirados / $totalItems) * 100, 2),
                "preparados" => $estados->preparados,
                "preparados_porcentaje" => $totalItems === 0 ? 0 : round(($estados->preparados / $totalItems) * 100, 2),
                "no_entregados" => $estados->no_entregados,
                "no_entregados_porcentaje" => $totalItems === 0 ? 0 : round(($estados->no_entregados / $totalItems) * 100, 2),
                "item_cantidad" => $totalItems
            ],
            "items" => $items,
            "parametros" => $parametros,
            "usuario" => $usuario
        ];
    }

    public function gestion(InformeExcelItemRequest $request){
        try {
            $data = $this->constructor($request);
            return response()->json($data);
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
    }
    
    public function gestionExcel(InformeExcelItemRequest $request){
        try {
            $data = $this->constructor($request);
           
            $nombreUsuario = isset($data["usuario"]["nombre"]) ? $data["usuario"]["nombre"] : explode('@', $data["usuario"]["email"])[0];
            $filename = Str::random(5) . '' .$nombreUsuario.'-'.now()->format('Y-m-d').'.xlsx'; // Generar un nombre único para el archivo
            $path = 'informes/items/' . $filename; // Ruta en la que se guardará el archivo en S3
            $export = new ReporteItemGestionExport($data, $filename);

            Excel::store( $export, $path, 's3', null, [
                'visibility' => 'public',
                // 'Expires' => time() + 60 * 60 * 24 * 1, // 1 dia
                // 'Expires' => time() + 60 * 1, // 1 minutos
            ]);
            
            $url = Storage::url($path); // Obtener la URL del archivo almacenado

            $consumoService =  new ConsumoService();
            $consumoService->guardarConsumoInforme($request->creado_por);

            return response()->json(['url' => $url]); 
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
    }
}
