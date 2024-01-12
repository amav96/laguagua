<?php


namespace App\Http\Services\Empresa;

use App\Models\UsuarioEmpresa;

class EmpresaService {

    public function usuarioPerteneceEmpresa(int $usuarioId, int $empresaId){
        return UsuarioEmpresa::where("usuario_id", $usuarioId)->where('empresa_id', $empresaId)->exists();
    }
}