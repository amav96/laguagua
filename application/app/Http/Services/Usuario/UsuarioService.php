<?php
namespace App\Http\Services\Usuario;

use App\Config\Seguridad\ValuePermiso;
use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use DB;

class UsuarioService {

    public function permisos(int $usuarioId){
        $usuario = User::find($usuarioId);
        
        if($usuario->email !== "alvaroamav96@gmail.com"){
            return [];
        }

        return collect(ValuePermiso::rolesPermisos())
        ->filter(fn($grupo) => $grupo["administrador"] === true)
        ->map(fn($grupo) => $grupo["nombre"])
        ->toArray();
    }

    public function findAll(array $parametros){
        
        // TODO: poner timezone dinamico por usuario admin
        $fechaArgentina = now()->setTimezone($parametros["time_zone"])->toDateString();

        $query = User::query();

        $query = User::with(["usuarioConsumo"])
                    ->withCount(['paradas as paradas_hoy' => function ($query) use($fechaArgentina) {
                        $query->whereDate('created_at', $fechaArgentina)
                            ->whereColumn('rider_id', 'usuarios.id')
                            ->select(DB::raw('COUNT(*)'));
                    }])
                    ->withCount(['paradas as paradas_total' => function ($query) {
                        $query->whereColumn('rider_id', 'usuarios.id')
                            ->select(DB::raw('COUNT(*)'));
                    }])
                    ->when(isset($parametros["usuario_id"]), function (Builder $q) use($parametros) : void {
                        $q->where('id', $parametros["usuario_id"]); 
                    })
                    ->when(isset($parametros["incluir"]), function (Builder $q) use($parametros) : void {
                        $q->with($parametros["incluir"]);
                    });

        if(isset($parametros["page"])){
            $query = $query->paginate();
        } else {
            $query = $query->get();
        }

        return $query;
    }

    public function update(User $usuario, array $request) : User{
        beginTransaction();
        try {

            $usuario->fill([
                "nombre"            => $request["nombre"],
                "pais_id"           => (int)$request["pais_id"],
            ]);
    
            $usuario->save();

        } catch (\Throwable $th) {
            rollBack();
            
            throw new BussinessException(AppErrors::USUARIO_ACTUALIZAR_ERROR_MESSAGE, AppErrors::USUARIO_ACTUALIZAR_ERROR_CODE);
        }

        commit();

        return $usuario;
    }
}