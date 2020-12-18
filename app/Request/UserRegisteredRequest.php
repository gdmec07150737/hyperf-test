<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRegisteredRequest extends FormRequest
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
            'email' => 'required|max:200|unique:user,email|email',
            'password' => 'required|max:50|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => '该email已存在，请换一个！',
            'email.email' => '该email不合法，请核对后再输入！',
        ];
    }
}
