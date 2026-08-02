<?php

namespace App\Services\WaterQuality\Contracts;

interface WaterQualityStrategy
{
    public function calculate(array $data): array;
}
