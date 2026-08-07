<?php

namespace App\Facade;

use App\Models\WaterSource;

class WaterSourceProfileFacade
{
    public function getSummary(WaterSource $waterSource): array
    {
        return [
            ['label' => 'Complaints',       'value' => $waterSource->complaints()->count()],
            ['label' => 'Quality Readings', 'value' => $waterSource->qualityReadings()->count()],
            ['label' => 'Alerts',           'value' => $waterSource->alerts()->count()],
        ];
    }
}