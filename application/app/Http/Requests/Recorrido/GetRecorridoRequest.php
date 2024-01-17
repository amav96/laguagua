<?php

namespace App\Http\Requests\Recorrido;

use App\Rules\Recorrido\PerteneceRecorridoRule;
use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class GetRecorridoRequest extends FormRequest
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
        $rules = [
            "inicio"    => "nullable|string",
            "rider_id"  => "nullable|integer|usuarios,id",
        ];

        return $rules;
    }
}
