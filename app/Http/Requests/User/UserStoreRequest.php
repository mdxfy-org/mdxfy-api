<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            ],
            'email' => 'required|email|unique:pgsql.hr.user|max:255',
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'password_confirm' => 'required|same:password',
            'terms_and_privacy_agreement' => 'required|accepted',
            'remember' => 'nullable|string',
        ];
    }
}
