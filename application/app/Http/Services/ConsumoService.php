<?php

namespace App\Http\Services;

use App\Models\UsuarioConsumo;

class ConsumoService {


    public function guardarConsumoOptimizar(int $usuarioId, int $cantidadParadas){
        $costo = $cantidadParadas > 12 ? 0.008 : 0.004;
        if(!UsuarioConsumo::where('usuario_id', $usuarioId)->exists()){
            UsuarioConsumo::create([
                "usuario_id"            => $usuarioId,
                "cantidad_optimizar"    => 1,
                "consumo_optimizar"     => $costo
            ]);
        } else {
            UsuarioConsumo::where('usuario_id', $usuarioId)->increment('cantidad_optimizar');
            UsuarioConsumo::where('usuario_id', $usuarioId)->increment('consumo_optimizar', $costo);
        }   
    }

    public function guardarConsumoDetectar(int $usuarioId){
        $costo = 0.0015;
        if(!UsuarioConsumo::where('usuario_id', $usuarioId)->exists()){
            UsuarioConsumo::create([
                "usuario_id"            => $usuarioId,
                "cantidad_detectar"    => 1,
                "consumo_detectar"     => $costo
            ]);
        } else {
            UsuarioConsumo::where('usuario_id', $usuarioId)->increment('cantidad_detectar');
            UsuarioConsumo::where('usuario_id', $usuarioId)->increment('consumo_detectar', $costo);
        }   
    }

    public function guardarConsumoInforme(int $usuarioId){

        if(!UsuarioConsumo::where('usuario_id', $usuarioId)->exists()){
            UsuarioConsumo::create([
                "usuario_id"            => $usuarioId,
                "cantidad_informes"      => 1,
            ]);
        } else {
            UsuarioConsumo::where('usuario_id', $usuarioId)->increment('cantidad_informes');
        }   
    }

}