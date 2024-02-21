<?php 
use Carbon\Carbon;
use App\Exceptions\AppErrors;
use App\Http\Services\Usuario\UsuarioService;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

function beginTransaction(){
    DB::beginTransaction();
}

function rollBack(){
    DB::rollBack();
}

function commit(){
    DB::commit();
}

function getNumbers(string $value){
    return preg_replace('/[^0-9]/', '', $value);
}

function setTimestampFieldDB(string $value){
    return Carbon::parse($value);
}

function autorizado(User $usuario, $permiso = '') {
    $permisosUsuario = (new UsuarioService)->permisos($usuario->id);
    if($permiso && !in_array($permiso, $permisosUsuario)){
        abort( response()->json(["message" => AppErrors::USUARIO_NO_AUTORIZADO_MENSAJE], 403) );
    }
    if($usuario["email"] !== 'alvaroamav96@gmail.com'){
        abort( response()->json(["message" => AppErrors::USUARIO_NO_AUTORIZADO_MENSAJE], 403) );
    }
}
