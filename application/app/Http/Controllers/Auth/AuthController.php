<?php

namespace App\Http\Controllers\Auth;

use App\Config\Seguridad\ValuePermiso;
use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Http\Services\Usuario\UsuarioService;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public $permisos = [];

    public function __construct(
        public AuthService $authService,
        public UsuarioService $usuarioService
    ){}

    public function autenticado(Request $request){
        
        $usuario = $request->user()->load(["empresas", "pais","rol"]);
        $usuario->permisos = $this->usuarioService->permisos($usuario->id);
        return response()->json(["autenticado" => $usuario]);
    }

    public function login (AuthLoginRequest $request) {

        try {

            list($usuario, $token) = $this->authService->login($request->all());
            $usuario->permisos = $this->usuarioService->permisos($usuario->id);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 400);
        }

    
        return response()->json([
            'usuario'   => $usuario,
            'token'     => $token
        ]);

    }

    public function googleAuthLogin(VerifyRequest $request){
        try {

            $verificacion = $this->authService->verifyGoogleToken($request["token"]);
            if(!$verificacion){
                throw new BussinessException(AppErrors::USER_ACCESS_TOKEN_INVALID_MESSAGE, AppErrors::USER_ACCESS_TOKEN_INVALID_CODE);
            }

            list($usuario, $token) = $this->authService->googleAuthLogin(["email" => $verificacion["email"]]);
            $usuario->permisos = $this->usuarioService->permisos($usuario->id);

            return response()->json([
                'usuario'   => $usuario,
                'token'     => $token
            ]);

            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 400);
        }
        
    }

    public function googleAuthRegistrar(VerifyRequest $request){
        try {

            $verificacion = $this->authService->verifyGoogleToken($request["token"]);

            if(!$verificacion){
                throw new BussinessException(AppErrors::USER_ACCESS_TOKEN_INVALID_MESSAGE, AppErrors::USER_ACCESS_TOKEN_INVALID_CODE);
            }

            if(User::where("email", $verificacion["email"])->exists()){
                throw new BussinessException(AppErrors::USER_EXISTS_MESSAGE, AppErrors::USER_EXISTS_CODE);
            }

            list($usuario, $token) = $this->authService->googleAuthRegistrar (["email" => $verificacion["email"]]);
            $usuario->permisos = $this->usuarioService->permisos($usuario->id);

            return response()->json([
                'usuario'   => $usuario,
                'token'     => $token
            ]);

            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 400);
        }
        
    }

    public function registrar (AuthRegisterRequest $request) {

        try {
            
            list($usuario, $token) = $this->authService->registrar($request->all());
            $usuario->permisos = $this->usuarioService->permisos($usuario->id);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 400);
        }

        return response()->json([
            'usuario'   => $usuario,
            'token'     => $token
        ]);
    }

    public function logout (Request $request) {
        $this->authService->logout($request);
        return response(['message' => 'You have been successfully logged out!'], 200);
    }

    public function usurpar(User $usuario, Request $request){

        $usuarioAutenticado = $request->user();
        
        autorizado($usuarioAutenticado, ValuePermiso::ADMINISTRACION_USURPAR_USUARIOS);

        $data["email"] = $usuario->email;

        list($usuarioUsurpado, $token) = $this->authService->usurpar($data);

        return response()->json([
            'usuario'   => $usuarioUsurpado,
            'token'     => $token
        ]);

    }
}
