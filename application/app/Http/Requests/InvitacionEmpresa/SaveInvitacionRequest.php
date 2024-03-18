<?php

namespace App\Http\Requests\InvitacionEmpresa;

use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class SaveInvitacionRequest extends FormRequest
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
            "email_invitado" => "required|string|email",
            "invitador_id"   => "required|integer|exists:usuarios,id",
            "rol_id"         => "required|integer|exists:roles,id",
            "empresa_id"     => "required|integer|exists:empresas,id"
        ];
    }
}
