<?php

namespace App\Services\WaterQuality;

use App\Models\QualityReading;
use App\Services\WaterQuality\Contracts\WaterQualityStrategy;
use InvalidArgumentException;

class GroundwaterStrategy implements WaterQualityStrategy
{
    private const ASSESSMENT_METHOD = 'Weighted Arithmetic WQI';

    private const PARAMETERS = [
        'ph',
        'tds',
        'hardness',
        'chloride',
        'sulphate',
        'nitrate',
        'iron',
        'manganese',
    ];

    private const STANDARDS = [
        'ph' => 9.0,
        'tds' => 1000.0,
        'hardness' => 500.0,
        'chloride' => 250.0,
        'sulphate' => 250.0,
        'nitrate' => 10.0,
        'iron' => 0.3,
        'manganese' => 0.1,
    ];

    private const IDEAL_VALUES = [
        'ph' => 7.0,
        'tds' => 0.0,
        'hardness' => 0.0,
        'chloride' => 0.0,
        'sulphate' => 0.0,
        'nitrate' => 0.0,
        'iron' => 0.0,
        'manganese' => 0.0,
    ];

    public function calculate(array $data): array
    {
        $this->validateInput($data);

        $k = $this->calculateProportionalityConstant();
        $weights = $this->calculateWeights($k);
        $subIndices = [];

        foreach (self::PARAMETERS as $parameter) {
            $qualityRating = $this->calculateQualityRating($parameter, (float) $data[$parameter]);
            $subIndices[$parameter] = $this->calculateSubIndex(
                $qualityRating,
                $weights[$parameter]
            );
        }

        $wqi = $this->calculateFinalWqi($subIndices);

        return [
            'assessment_method' => self::ASSESSMENT_METHOD,
            'wqi' => $wqi,
            'classification' => $this->determineClassification($wqi),
            'status' => $this->determineStatus($wqi),
        ];
    }

    private function validateInput(array $data): void
    {
        foreach (self::PARAMETERS as $parameter) {
            if (! array_key_exists($parameter, $data)) {
                throw new InvalidArgumentException("Missing groundwater parameter: {$parameter}");
            }
        }
    }

    private function calculateProportionalityConstant(): float
    {
        $reciprocalTotal = 0.0;

        foreach (self::STANDARDS as $standard) {
            $reciprocalTotal += 1 / $standard;
        }

        return 1 / $reciprocalTotal;
    }

    private function calculateWeights(float $k): array
    {
        $weights = [];

        foreach (self::STANDARDS as $parameter => $standard) {
            $weights[$parameter] = $k / $standard;
        }

        return $weights;
    }

    private function calculateQualityRating(string $parameter, float $value): float
    {
        $standard = self::STANDARDS[$parameter];
        $idealValue = self::IDEAL_VALUES[$parameter];
        $qualityRating = 100.0 * (($value - $idealValue) / ($standard - $idealValue));

        if ($parameter === 'ph' && $qualityRating < 0) {
            return abs($qualityRating);
        }

        return $qualityRating;
    }

    private function calculateSubIndex(float $qualityRating, float $weight): float
    {
        return $qualityRating * $weight;
    }

    private function calculateFinalWqi(array $subIndices): float
    {
        return array_sum($subIndices);
    }

    private function determineClassification(float $wqi): string
    {
        if ($wqi <= 25) {
            return 'Excellent';
        }

        if ($wqi <= 50) {
            return 'Good';
        }

        if ($wqi <= 75) {
            return 'Poor';
        }

        if ($wqi <= 100) {
            return 'Very Poor';
        }

        return 'Unsuitable for Drinking';
    }

    private function determineStatus(float $wqi): string
    {
        $classification = $this->determineClassification($wqi);

        switch ($classification) {
            case 'Excellent':
            case 'Good':
                return QualityReading::STATUS_SAFE;

            case 'Poor':
                return QualityReading::STATUS_WARNING;

            default:
                return QualityReading::STATUS_CRITICAL;
        }
    }
}
