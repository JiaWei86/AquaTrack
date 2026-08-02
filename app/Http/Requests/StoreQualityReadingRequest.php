<?php

namespace App\Http\Requests;

use App\Models\WaterSource;
use Illuminate\Foundation\Http\FormRequest;

class StoreQualityReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'inspector_id' => ['required', 'exists:users,id'],
            'water_source_id' => ['required', 'exists:water_sources,id'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];

        $waterSource = WaterSource::find($this->input('water_source_id'));

        if (! $waterSource) {
            return $rules;
        }

        if (in_array($waterSource->source_type, [
            'River',
            'Lake',
            'Reservoir',
        ], true)) {
            $rules = array_merge($rules, [
                'ph' => ['required', 'numeric', 'between:0,14'],
                'temperature' => ['required', 'numeric', 'min:0'],
                'dissolved_oxygen' => ['required', 'numeric', 'min:0'],
                'bod' => ['required', 'numeric', 'min:0'],
                'cod' => ['required', 'numeric', 'min:0'],
                'suspended_solids' => ['required', 'numeric', 'min:0'],
                'ammoniacal_nitrogen' => ['required', 'numeric', 'min:0'],
            ]);
        }

        if ($waterSource->source_type === 'Well') {
            $rules = array_merge($rules, [
                'ph' => ['required', 'numeric', 'between:0,14'],
                'tds' => ['required', 'numeric', 'min:0'],
                'hardness' => ['required', 'numeric', 'min:0'],
                'chloride' => ['required', 'numeric', 'min:0'],
                'sulphate' => ['required', 'numeric', 'min:0'],
                'nitrate' => ['required', 'numeric', 'min:0'],
                'iron' => ['required', 'numeric', 'min:0'],
                'manganese' => ['required', 'numeric', 'min:0'],
            ]);
        }

        if ($waterSource->source_type === 'Community Tap') {
            $rules = array_merge($rules, [
                'ph' => ['required', 'numeric', 'between:0,14'],
                'turbidity' => ['required', 'numeric', 'min:0'],
                'colour' => ['required', 'numeric', 'min:0'],
                'residual_chlorine' => ['required', 'numeric', 'min:0'],
                'aluminium' => ['required', 'numeric', 'min:0'],
                'total_coliform' => ['required', 'boolean'],
                'e_coli' => ['required', 'boolean'],
            ]);
        }

        return $rules;
    }
}
