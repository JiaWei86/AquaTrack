<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'regex:/^(?:\+?6?01)[02-46-9]\d{7,8}$/'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Malaysian phone number, e.g. 0123456789 or +60123456789.',
            'password.regex' => 'The password must contain at least one uppercase letter and one lowercase letter.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
