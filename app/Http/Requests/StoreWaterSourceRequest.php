<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWaterSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization (RBAC): only administrators manage water sources
        return $this->user() && $this->user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            'source_name' => ['required', 'string', 'max:255'],

            // Mirrors the DB enum exactly — app-layer + DB-layer both reject
            // an invalid type, so no unexpected value can ever reach the query.
            'source_type' => ['required', 'string', Rule::in([
                'River', 'Lake', 'Reservoir', 'Well', 'Community Tap',
            ])],

            'location'  => ['required', 'string', 'max:255'],
            'latitude'  => ['required', 'numeric', 'between:0.5,7.5', 'regex:/^-?\d+\.\d{5,7}$/'],
            'longitude' => ['required', 'numeric', 'between:99,120', 'regex:/^-?\d+\.\d{5,7}$/'],
            'notes'     => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'source_type.in'    => 'Please select a valid source type.',
            'latitude.regex'  => 'Latitude must include at least 5 decimal places (e.g. 3.28126).',
            'longitude.regex' => 'Longitude must include at least 5 decimal places (e.g. 101.53349).',
        ];
    }
}