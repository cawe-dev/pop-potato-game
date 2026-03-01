<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GuestLoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:3', 'max:50', 'unique:users,nickname'],
        ];
    }

    public function messages(): array
    {
        return [
            'nickname.required' => 'Please enter a nickname to join the game.',
            'nickname.min' => 'Your nickname must be at least 3 characters long.',
            'nickname.unique' => 'This nickname is already in use. Try something else!',
        ];
    }
}
