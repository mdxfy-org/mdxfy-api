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
            'name' => 'required|string|min:1|max:255',
            'username' => [
                'required',
                'string',
                'unique:pgsql.hr.user',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+$/',
            ],
            'language' => 'nuable|string|max:10',
        ];
    }
}
