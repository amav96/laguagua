<?php 

namespace App\Http\Services;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Jobs\RecuperarPassword;
use App\Models\Empresa;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Suscripcion;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class AuthService {

    public function registrar (array $request){

        DB::beginTransaction();

        try {

            $usuario = User::create([
                'nombre'            => $request['nombre'],
                'email'             => $request['email'],
                'password'          => Hash::make($request['password']),
            ]);

            UsuarioEmpresa::create([
                "usuario_id"    => $usuario->id,
                "empresa_id"    => 1
            ]);

    
            Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(1));
            $token = $usuario->createToken('authToken')->accessToken;
            
        } catch(Exception $e) {
            DB::rollback();
            throw new BussinessException(AppErrors::ERROR_REGISTRO_MESSAGE, AppErrors::ERROR_REGISTRO_CODE);
        }

        DB::commit();

        return [$usuario, $token];

    }

    public function login (Request $request) {
        $usuario = User::where('email', $request->email)->first();

        if(!$usuario){
            throw new BussinessException(AppErrors::WRONG_USER_PASS_MESSAGE, AppErrors::WRONG_USER_PASS_CODE);
        }

        if (!Hash::check($request->password, $usuario->password)) {
            throw new BussinessException(
                AppErrors::WRONG_USER_PASS_MESSAGE,
                AppErrors::WRONG_USER_PASS_CODE
            );
        }

        $token = $usuario->createToken('authToken')->accessToken;

        return [$usuario, $token];

    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
    }


}