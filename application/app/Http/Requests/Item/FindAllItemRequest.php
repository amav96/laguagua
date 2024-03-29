<?php

namespace App\Http\Requests\Item;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class FindAllItemRequest extends FormRequest
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
           'rider_id'     => "nullable|integer|exists:usuarios,id",
           "item_id"        => "nullable|integer|exists:items,id",
           "empresa_id"     => "nullable|integer",
           "busqueda"       => "nullable|string|max:100",
           "track_id"       => "nullable|string|max:100"
        ];
    }
}
