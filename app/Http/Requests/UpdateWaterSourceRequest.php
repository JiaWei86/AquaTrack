<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWaterSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            // "sometimes" = only validate a field if the client actually sent
            // it (a genuine partial update); "required" beside it means IF
            // sent, it can't be sent empty. Prevents a partial PATCH from
            // wiping a field by accident, without forcing a full PUT payload.
            'source_name' => ['sometimes', 'required', 'string', 'max:255'],
            'source_type' => ['sometimes', 'required', 'string', Rule::in([
                'River', 'Lake', 'Reservoir', 'Well', 'Community Tap',
            ])],
            'location'  => ['sometimes', 'required', 'string', 'max:255'],
            'latitude'  => ['sometimes', 'required', 'numeric', 'between:0.5,7.5', 'regex:/^-?\d+\.\d{5,7}$/'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:99,120', 'regex:/^-?\d+\.\d{5,7}$/'],
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