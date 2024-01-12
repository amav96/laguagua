<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\AppErrors;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthLoginRequest extends FormRequest
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
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ];
    }

     protected function failedValidation(Validator $validator)
    {
        $messages = [
            "code" => AppErrors::WRONG_INPUT_DATA_CODE,
            "messages" => $validator->errors()->get('*')
        ];
        throw new HttpResponseException(response($messages, 400));
    }


}
