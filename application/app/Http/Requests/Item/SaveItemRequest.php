<?php

namespace App\Http\Requests\Item;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SaveItemRequest extends FormRequest
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
            "items"                     => "required|array",
            "items.*.tipo_item_id"      => "required|integer|exists:tipos_items,id",
            "items.*.proveedor_item_id" => "required|integer|exists:proveedores_items,id",
            "items.*.destinatario"      => "nullable|string|max:255",
            "items.*.estado_item_id"    => "nullable|integer|exists:estados_items,id",
            "items.*.track_id"          => "nullable|string|max:255",
            "items.*.cliente_id"        => "nullable|integer|exists:clientes,id",
            "parada_id"                 => "nullable|integer|exists:paradas,id",
            "rider_id"                  => "nullable|integer|exists:usuarios,id",
        ];

        return $rules;
    }
}
