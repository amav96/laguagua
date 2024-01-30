<?php

namespace App\Http\Requests\Recorrido;

use App\Rules\Usuario\PerteneceUsuarioRule;
use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class GenerarInformeRecorridoRequest extends FormRequest
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
            "recorrido_id" => "required|integer|exists:recorridos,id",
            "rider_id"        => [
                "required",
                "integer",
                "exists:usuarios,id",
                new PerteneceUsuarioRule()
            ]
        ];
    }
}
