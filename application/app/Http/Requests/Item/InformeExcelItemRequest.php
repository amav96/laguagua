<?php

namespace App\Http\Requests\Item;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class InformeExcelItemRequest extends FormRequest
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
            "rider_id"    => "required|exists:usuarios,id",
            "fecha_inicio"  => "required|date",
            "fecha_fin"     => "required|date",
            "page"          => "nullable|integer",
            "empresa_id"    => "nullable|integer",
        ];
    }
}
