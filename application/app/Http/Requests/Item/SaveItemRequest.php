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

        if ($this->isMethod("PATCH")) {
            $rules = [
                "item_estado_id"    => "required|integer|exists:items_estados,id",
                "parada_id"         => "nullable|integer|exists:paradas,id",
            ];
            return $rules;
        }

        $rules = [
            "empresa_id"        => "required|integer|exists:empresas,id",
            "parada_id"         => "nullable|integer|exists:paradas,id",
            "rider_id"          => "nullable|integer|exists:usuarios,id",
            "item_tipo_id"      => "required|integer|exists:items_tipos,id",
            "item_proveedor_id" => "required|integer|exists:items_proveedores,id",
            "destinatario"      => "nullable|string|max:255",
            "track_id"          => "nullable|string|max:255",
        ];
    
        if ($this->isMethod("POST")) {
            $rules += [
                "item_estado_id"    => "nullable|integer|exists:items_estados,id",
                "cliente_id"        => "nullable|integer|exists:clientes,id",
            ];
        }
    
        if ($this->isMethod("PUT")) {
            $rules += [
                "item_estado_id"    => "required|integer|exists:items_estados,id",
            ];
        }

        return $rules;
    }
}
