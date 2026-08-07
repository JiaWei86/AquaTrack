<?php

namespace App\Http\Requests;

use App\Models\WaterSource;
use Illuminate\Validation\Rule;
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
            'inspector_id' => [
                    'required',
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->where('role', 'Inspector');
                    }),
                ],
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
                'temperature' => ['required', 'numeric', 'between:0,50'],
                'dissolved_oxygen' => ['required', 'numeric', 'between:0,20'],
                'bod' => ['required', 'numeric', 'between:0,100'],
                'cod' => ['required', 'numeric', 'between:0,500'],
                'suspended_solids' => ['required', 'numeric', 'between:0,1000'],
                'ammoniacal_nitrogen' => ['required', 'numeric', 'between:0,100'],
            ]);
        }

        if ($waterSource->source_type === 'Well') {
            $rules = array_merge($rules, [
                'ph' => ['required', 'numeric', 'between:0,14'],
                'tds' => ['required', 'numeric', 'between:0,5000'],
                'hardness' => ['required', 'numeric', 'between:0,1000'],
                'chloride' => ['required', 'numeric', 'between:0,1000'],
                'sulphate' => ['required', 'numeric', 'between:0,1000'],
                'nitrate' => ['required', 'numeric', 'between:0,100'],
                'iron' => ['required', 'numeric', 'between:0,10'],
                'manganese' => ['required', 'numeric', 'between:0,10'],
            ]);
        }

        if ($waterSource->source_type === 'Community Tap') {
            $rules = array_merge($rules, [
                'ph' => ['required', 'numeric', 'between:0,14'],
                'turbidity' => ['required', 'numeric', 'between:0,100'],
                'colour' => ['required', 'numeric', 'between:0,100'],
                'residual_chlorine' => ['required', 'numeric', 'between:0,10'],
                'aluminium' => ['required', 'numeric', 'between:0,10'],
                'total_coliform' => ['required', 'boolean'],
                'e_coli' => ['required', 'boolean'],
            ]);
        }

        return $rules;
    }
}
