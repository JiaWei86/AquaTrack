<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateResidentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:Active,Inactive'],
        ];
    }
}
