<?php

namespace App\Http\Requests\Item;

use App\Rules\Item\PerteneceItemUsuarioRule;
use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SaveItemComprobanteRequest extends FormRequest
{
    use RequestValidationHandler;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        if($this->isMethod("POST")){
            return [
                "nombre_archivo" => "required|string",
                "item_id"        => [
                    "required",
                    "integer",
                    "exists:items,id",
                    new PerteneceItemUsuarioRule()
                ]
            ];
        }
        
        if($this->isMethod("PATCH")){
            return [
                "path" => "required|string",
                "item_id"        => [
                    "required",
                    "integer",
                    "exists:items,id",
                    new PerteneceItemUsuarioRule()
                ]
            ];
        }
    }
}
