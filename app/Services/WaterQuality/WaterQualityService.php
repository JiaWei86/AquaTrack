<?php

namespace App\Services\WaterQuality;

class WaterQualityService
{
    public function __construct(private WaterQualityStrategyFactory $strategyFactory)
    {
    }

    public function evaluate(string $waterSourceType, array $readingData): array
    {
        $strategy = $this->strategyFactory->create($waterSourceType);
        $result = $strategy->calculate($readingData);

        return $result;
    }
}
