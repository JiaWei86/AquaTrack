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
            'latitude'  => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'notes'     => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'source_type.in'    => 'Please select a valid source type.',
            'latitude.between'  => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}