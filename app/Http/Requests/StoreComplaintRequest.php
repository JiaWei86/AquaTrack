<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization (RBAC): only residents can submit complaints
        return $this->user() && $this->user()->isResident();
    }

    public function rules(): array
    {
        return [
            // Input Validation
            'water_source_id' => ['required', 'integer', 'exists:water_sources,id'],
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['required', 'string', 'max:2000'],

            // File Upload Validation
            'photo' => [
                'nullable',
                'image',                  // verifies the actual file content (real MIME type), not just the extension
                'mimes:jpg,jpeg,png',     // only JPG / PNG images are allowed
                'max:2048',               // maximum 2MB, prevents oversized file attacks
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.image' => 'The uploaded file must be a valid image.',
            'photo.mimes' => 'Only JPG and PNG images are allowed.',
            'photo.max'   => 'The image must not exceed 2MB.',
        ];
    }
}