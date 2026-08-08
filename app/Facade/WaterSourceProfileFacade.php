<?php

namespace App\Facade;

use App\Models\WaterSource;

class WaterSourceProfileFacade
{
    public function getSummary(WaterSource $waterSource): array
    {
        return [
            [
                'label' => 'Complaints',
                'value' => $waterSource->complaints()->count(),
                'link'  => route('water-sources.summary', [$waterSource, 'complaints']),
            ],
            [
                'label' => 'Quality Readings',
                'value' => $waterSource->qualityReadings()->count(),
                'link'  => route('water-sources.summary', [$waterSource, 'quality-readings']),
            ],
            [
                'label' => 'Alerts',
                'value' => $waterSource->alerts()->count(),
                'link'  => route('water-sources.summary', [$waterSource, 'alerts']),
            ],
        ];
    
    }
}