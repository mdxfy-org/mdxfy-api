<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'unique:pgsql.hr.user',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
            ], ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name_required',
            'username.required' => 'username_required',
            'username.unique' => 'username_already_exists',
            'username.regex' => 'invalid_username',
        ];
    }
}
