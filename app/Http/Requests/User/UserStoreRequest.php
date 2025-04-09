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
            'number' => 'regex:/^\d{13}$/',
            'email' => 'required|email|unique:pgsql.hr.user|max:255',
            'password' => [
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

    public function messages(): array
    {
        return [
            'name.required' => 'name_required',
            'username.required' => 'username_required',
            'username.unique' => 'username_already_exists',
            'username.regex' => 'invalid_username',
            'number.required' => 'number_required',
            'number.regex' => 'invalid_number',
            'email.email' => 'invalid_email',
            'email.unique' => 'email_already_exists',
            'password.required' => 'password_required',
            'password.min' => 'password_length',
            'password.regex' => 'password_character',
            'password_confirm.required' => 'password_confirm_required',
            'password_confirm.same' => 'password_not_coincide',
            'terms_and_privacy_agreement.required' => 'terms_and_privacy_agreement_required',
            'terms_and_privacy_agreement.accepted' => 'terms_and_privacy_agreement_not_accepted',
        ];
    }
}
