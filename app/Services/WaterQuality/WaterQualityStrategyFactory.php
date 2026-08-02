<?php

namespace App\Services\WaterQuality;

use App\Services\WaterQuality\Contracts\WaterQualityStrategy;
use InvalidArgumentException;

class WaterQualityStrategyFactory
{
    /**
     * Create the appropriate water quality strategy for a water source type.
     *
     * @throws InvalidArgumentException
     */
    public function create(string $waterSourceType): WaterQualityStrategy
    {
        $waterSourceType = strtolower(trim($waterSourceType));

        return match ($waterSourceType) {
            'river', 'lake' , 'reservoir'=> new DoeWqiStrategy(),
            'well' => new GroundwaterStrategy(),
            'community tap' => new ComplianceStrategy(),
            default => throw new InvalidArgumentException("Unsupported water source type: {$waterSourceType}"),
        };
    }
}
