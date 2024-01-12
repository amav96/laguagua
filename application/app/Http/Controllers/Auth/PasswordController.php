<?php

namespace App\Http\Controllers\Auth;

use App\Config\AppSuccess;
use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use App\Http\Requests\Auth\ResetearPasswordRequest;
use App\Http\Services\PasswordService;

class PasswordController extends Controller
{
    public function __construct(
        public PasswordService $passwordService,
    )
    {
        
    }


    public function resetearPassword(ResetearPasswordRequest $request){
        try {
            $this->passwordService->resetearPassword($request->all(), $request->user());
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 400);
        }

        return response(['message' => AppSuccess::PASSWORD_ACTUALIZADA_MESSAGE], 200);
    }
}
