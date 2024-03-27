<?php

namespace App\Http\Controllers\Empresa;

use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Empresa\SaveEmpresaRequest;
use App\Http\Services\Empresa\EmpresaService;
use App\Models\Empresa;
use App\Models\Rol;
use App\Models\UsuarioEmpresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{

    public function __construct(
        public EmpresaService $empresaService 
    )
    {}

    public function findAll(Request $request){
        
        $usuario = $request->user();
        $data = $request->all();
        $usuarioEmpresas = UsuarioEmpresa::where('usuario_id', $usuario->id)->get();
        $data["empresa_id"] = array_merge($usuarioEmpresas->pluck("empresa_id")->toArray(), [Empresa::INDEPENDIENTE]);


        try {
            $empresas =   $this->empresaService->findAll($data);
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($empresas);
    }

    public function create(SaveEmpresaRequest $request){

        $data = $request->all();
        $data["usuario_id"] = $request->user()->id;

        try {
            $nuevaEmpresa =   $this->empresaService->create($data);
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($nuevaEmpresa);
    }

    public function update(Empresa $empresa,SaveEmpresaRequest $request){

        $data = $request->all();
        
        try {
            $nuevaEmpresa =   $this->empresaService->update($empresa, $data);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($nuevaEmpresa);
    }

    public function delete(Empresa $empresa){

      
        try {
            $nuevaEmpresa =   $this->empresaService->delete($empresa);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(),  400);
        }  

        return response()->json($nuevaEmpresa);
    }

  
}
