<?php

namespace App\Rules\Item;

use App\Exceptions\AppErrors;
use App\Models\Item;
use App\Traits\RequestValidationHandler;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class PerteneceItemUsuarioRule implements ValidationRule 
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
        if(!Item::where("id", $value)->where('rider_id', $usuarioAutenticado->id)->exists()){
            $this->setCustomValidation(true);
            $this->setCustomCode(AppErrors::ITEM_NO_PERTECE_USUARIO_CODE);
            $this->setCustomMessage(AppErrors::ITEM_NO_PERTECE_USUARIO_MESSAGE);
            $this->throwExcepcionValidation();
        }
    }
}
