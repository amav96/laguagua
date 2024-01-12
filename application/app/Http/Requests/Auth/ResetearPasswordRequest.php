<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetearPasswordRequest extends FormRequest
{
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
            "usuario_id"            => 'required|integer|exists:usuarios,id',
            "email"                 => 'required|email|exists:usuarios,email',
            "codigo_verificador"    => 'required|integer',
            // "token"                 => 'required|string',
            "password"              => 'required|confirmed',
        ];
    }
}
