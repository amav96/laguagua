<?php

namespace App\Http\Requests\Parada;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SaveParadaComprobanteRequest extends FormRequest
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
                "parada_id"        => [
                    "required",
                    "integer",
                    "exists:paradas,id",
                ]
            ];
        }
        
        if($this->isMethod("PATCH")){
            return [
                "path" => "required|string",
                "parada_id"        => [
                    "required",
                    "integer",
                    "exists:paradas,id",
                ]
            ];
        }
    }
}
