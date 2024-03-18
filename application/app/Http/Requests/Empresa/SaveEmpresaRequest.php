<?php

namespace App\Http\Requests\Empresa;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SaveEmpresaRequest extends FormRequest
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
            "nombre" => "required|string|max:100"
        ];
    }
}
