<?php

namespace App\Rules\Usuario;

use App\Exceptions\AppErrors;
use App\Traits\RequestValidationHandler;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PerteneceUsuarioRule implements ValidationRule
{
    use RequestValidationHandler;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $usuarioAutenticado = request()->user();
        if($usuarioAutenticado->id !== $value){
            $this->setCustomValidation(true);
            $this->setCustomCode(AppErrors::USUARIO_NO_TE_PERTENECE_CODE);
            $this->setCustomMessage(AppErrors::USUARIO_NO_TE_PERTENECE_MESSAGE);
            $this->throwExcepcionValidation();
        }
    }
}
