<?php 

namespace App\Http\Services;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Jobs\RecuperarPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordService {


    public function resetearPassword(array $request, User $usuario){
        
        $usuario->password = Hash::make($request["password"]);
        $usuario->save();

        return [$usuario];

    }
}