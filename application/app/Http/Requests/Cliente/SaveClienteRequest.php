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
            "numero_documento"      => "required|string|max:30",
            "codigo_area_id"        => [
                "nullable",
                "integer",
                "max_digits:2",
                "exists:codigos_area,id"
            ],
            "nombre"                => "nullable|string",
            "numero_celular"        => "nullable|string|max:15",
            "numero_fijo"           => "nullable|string|max:20",
            "empresa_id"            => "required|integer|exists:empresas,id"
        ];

        if(request()->numero_celular){
            unset($rules["codigo_area_id"]["nullable"]);
            $rules["codigo_area_id"][] = "required";
        }

    
        return $rules;
    }

    protected function passedValidation()
    {
        $transformer = new ClienteRequestTransformer($this->validated());
        $formattedData = $transformer->transform();

        $this->replace($formattedData);
    }
}
