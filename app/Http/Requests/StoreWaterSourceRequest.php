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
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
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