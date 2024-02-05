<?php

namespace App\Http\Requests\Cliente;

use App\Http\RequestTransformer\Cliente\ClienteRequestTransformer;
use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SaveClienteRequest extends FormRequest
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
            "tipo_documento_id"     => "required|integer|max_digits:1|exists:tipos_documentos,id",
            "numero_documento"      => "nullable|string|max:30",
            "observaciones"         => "nullable|string",
            "clientes_numeros" => [
                "nullable",
                "array",
            ],
            "clientes_numeros.*.codigo_area_id" => [
                "required_with:clientes_numeros",
                "integer",
                "max_digits:2",
                "exists:codigos_area,id",
            ],
            "clientes_numeros.*.numero" => [
                "required_with:clientes_numeros",
                "string",
                "max:30"
            ],
            "clientes_numeros.*.id" => [
                "nullable",
                "integer",
                "max_digits:100"
            ],
            "nombre"                => "nullable|string",
            "empresa_id"            => "required|integer|exists:empresas,id",
            
        ];


        return $rules;
    }

    protected function passedValidation()
    {
        $transformer = new ClienteRequestTransformer($this->validated());
        $formattedData = $transformer->transform();

        $this->replace($formattedData);
    }
}
