<?php 

namespace App\Http\Services;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use GuzzleHttp\Client;

class AuthService {

    public function registrar (array $request){

        DB::beginTransaction();

        try {

            $usuario = User::create([
                'nombre'            => $request['nombre'],
                'email'             => $request['email'],
                'password'          => Hash::make($request['password']),
                'pais_id'           => $request['pais_id']
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

    public function login (array $request) {
        $usuario = User::where('email', $request["email"])->first();

        if(!$usuario){
            throw new BussinessException(AppErrors::WRONG_USER_PASS_MESSAGE, AppErrors::WRONG_USER_PASS_CODE);
        }

        if (!Hash::check($request["password"], $usuario->password)) {
            throw new BussinessException(
                AppErrors::WRONG_USER_PASS_MESSAGE,
                AppErrors::WRONG_USER_PASS_CODE
            );
        }

        $token = $usuario->createToken('authToken')->accessToken;

        return [$usuario, $token];

    }

    public function googleAuthLogin(array $request){

        $usuario = User::where('email', $request["email"])->first();

        if(!$usuario){
            throw new BussinessException(AppErrors::WRONG_USER_PASS_MESSAGE, AppErrors::WRONG_USER_PASS_CODE);
        }

        Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(1));
        $token = $usuario->createToken('authToken')->accessToken;

        return [$usuario, $token];

    }

    public function googleAuthRegistrar(array $request) {
        DB::beginTransaction();
       
        try {

            $usuario = User::create([
                'email'             => $request['email'],
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

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
    }

    public function verifyGoogleToken(string $accessToken) : array | bool
    {
        
       // Realizar una solicitud a la API de Google para validar el access_token
       $client = new Client();
       $response = $client->get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
           'query' => [
               'access_token' => $accessToken,
           ],
       ]);

       // Verificar la respuesta de la API de Google
       $responseData = json_decode($response->getBody(), true);
       
        if (isset($responseData['error']) && isset($responseData['email_verified']) && $responseData['email_verified'] !== 'true') {
            // El access_token no es válido
            return false;

        } else {
            $aud = $responseData["aud"];
            if($aud !== config("services.google.client_id")){
                return false;
            }
            
            return $responseData;
        }
    }


}