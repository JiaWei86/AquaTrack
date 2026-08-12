<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Updating a quality reading requires the exact same authorization and
 * validation rules as creating one (Administrator may select any Inspector;
 * an Inspector may only submit their own inspector_id; the required
 * measurement fields depend on the same server-resolved water source type).
 * Reuses StoreQualityReadingRequest entirely, adding one update-only rule:
 * an Inspector may not move the reading to a different water source.
 */
class UpdateQualityReadingRequest extends StoreQualityReadingRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $user = $this->user();
        $qualityReading = $this->route('quality_reading');

        if ($user && $user->isInspector() && $qualityReading) {
            $rules['water_source_id'][] = Rule::in([$qualityReading->water_source_id]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'water_source_id.in' => 'Inspectors may not change the water source of an existing quality reading.',
        ]);
    }
}
