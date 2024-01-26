<?php

namespace App\Http\Requests\Recorrido;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrigenActualRequest extends FormRequest
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
            "origen_actual_lat" => "required|numeric",
            "origen_actual_lng" => "required|numeric",
            "origen_actual_formateado" => "required|string",
        ];
    }
}
