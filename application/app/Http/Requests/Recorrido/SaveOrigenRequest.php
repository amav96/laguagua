<?php

namespace App\Http\Requests\Recorrido;

use App\Exceptions\AppErrors;
use App\Traits\RequestValidationHandler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveOrigenRequest extends FormRequest
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
       
        return [
            "origen_lat"        => "required|numeric",
            "origen_lng"        => "required|numeric",
            "origen_formateado" => "required|string",
            "origen_auto"       => "required|integer"
        ];
    }

}
