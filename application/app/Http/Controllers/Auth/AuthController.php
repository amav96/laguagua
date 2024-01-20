<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\BussinessException;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct(
        public AuthService $authService,
    )
    {}

    public function autenticado(Request $request){
        $usuario = $request->user()->load("empresas");
        return response()->json(["autenticado" => $usuario]);
    }

    public function login (AuthLoginRequest $request) {

        try {

            list($usuario, $token) = $this->authService->login($request);
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 400);
        }

        return response()->json([
            'usuario'   => $usuario,
            'token'     => $token
        ]);

    }

    public function registrar (AuthRegisterRequest $request) {

        try {
            
            list($usuario, $token) = $this->authService->registrar($request->all());

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


}
