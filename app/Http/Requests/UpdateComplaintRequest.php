<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization (RBAC): only administrators can update complaint status
        return $this->user() && $this->user()->isAdministrator();
    }

    public function rules(): array
    {
        return [
            // Input Validation: status must match one of the enum values defined in the migration
            'status' => ['required', Rule::in(['Pending', 'Investigating', 'Resolved', 'Rejected'])],
        ];
    }
}