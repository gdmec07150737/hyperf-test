<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => 'required|max:200|email',
            'password' => 'required|max:50|min:6',
            'id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => '该email不合法，请核对后再输入！',
        ];
    }
}
