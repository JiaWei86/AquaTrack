<?php

namespace App\Services\WaterQuality;

use App\Models\QualityReading;
use App\Services\WaterQuality\Contracts\WaterQualityStrategy;
use InvalidArgumentException;

class ComplianceStrategy implements WaterQualityStrategy
{
    private const ASSESSMENT_METHOD = 'Compliance Assessment';

    private const TYPE_MAXIMUM = 'maximum';
    private const TYPE_RANGE = 'range';
    private const TYPE_ABSENCE = 'absence';

    private const PARAMETERS = [
        'ph',
        'turbidity',
        'colour',
        'residual_chlorine',
        'aluminium',
        'total_coliform',
        'e_coli',
    ];

    private const PARAMETER_TYPES = [
        'ph' => self::TYPE_RANGE,
        'turbidity' => self::TYPE_MAXIMUM,
        'colour' => self::TYPE_MAXIMUM,
        'residual_chlorine' => self::TYPE_RANGE,
        'aluminium' => self::TYPE_MAXIMUM,
        'total_coliform' => self::TYPE_ABSENCE,
        'e_coli' => self::TYPE_ABSENCE,
    ];

    private const STANDARDS = [
        'ph' => ['minimum' => 6.5, 'maximum' => 9.0],
        'turbidity' => ['maximum' => 5.0],
        'colour' => ['maximum' => 15.0],
        'residual_chlorine' => ['minimum' => 0.2, 'maximum' => 5.0],
        'aluminium' => ['maximum' => 0.2],
        'total_coliform' => ['required_absence' => true],
        'e_coli' => ['required_absence' => true],
    ];

    private const CRITICAL_PARAMETERS = [
        'total_coliform',
        'e_coli',
        'residual_chlorine',
    ];

    public function calculate(array $data): array
    {
        $this->validateInput($data);

        $passedParameters = 0;
        $failedParameters = 0;
        $failedItems = [];

        foreach (self::PARAMETERS as $parameter) {
            $passed = $this->evaluateParameter(
                $parameter,
                $data[$parameter]
            );

            if ($passed) {
                $passedParameters++;
            } else {
                $failedParameters++;
                $failedItems[] = $parameter;
            }
        }

        $compliancePercentage = $this->calculateCompliancePercentage(
            $passedParameters,
            $failedParameters
        );

        return [
            'assessment_method' => self::ASSESSMENT_METHOD,
            'wqi' => null,
            'compliance_percentage' => $compliancePercentage,
            'passed_parameters' => $passedParameters,
            'failed_parameters' => $failedParameters,
            'failed_items' => $failedItems,
            'classification' => $this->determineClassification($failedItems),
            'status' => $this->determineStatus($failedItems),
        ];
    }

    private function validateInput(array $data): void
    {
        foreach (self::PARAMETERS as $parameter) {
            if (! array_key_exists($parameter, $data)) {
                throw new InvalidArgumentException("Missing compliance parameter: {$parameter}");
            }
        }
    }

    private function evaluateParameter(string $parameter, mixed $value): bool
    {
        $type = self::PARAMETER_TYPES[$parameter];

        return match ($type) {
            self::TYPE_MAXIMUM => $this->evaluateMaximum($parameter, (float) $value),
            self::TYPE_RANGE => $this->evaluateRange($parameter, (float) $value),
            self::TYPE_ABSENCE => $this->evaluateAbsence($parameter, (bool) $value),
        };
    }

    private function evaluateMaximum(string $parameter, float $value): bool
    {
        return $value <= self::STANDARDS[$parameter]['maximum'];
    }

    private function evaluateRange(string $parameter, float $value): bool
    {
        return $value >= self::STANDARDS[$parameter]['minimum']
            && $value <= self::STANDARDS[$parameter]['maximum'];
    }

    private function evaluateAbsence(string $parameter, bool $value): bool
    {
        return $value === false;
    }

    private function calculateCompliancePercentage(int $passedParameters, int $failedParameters): float
    {
        $totalParameters = $passedParameters + $failedParameters;

        if ($totalParameters === 0) {
            return 0.0;
        }

        return ($passedParameters / $totalParameters) * 100;
    }

    private function determineClassification(array $failedItems): string
    {
        return empty($failedItems) ? 'Compliant' : 'Non-Compliant';
    }

    private function determineStatus(array $failedItems): string
    {
        if (empty($failedItems)) {
            return QualityReading::STATUS_SAFE;
        }

        foreach ($failedItems as $failedItem) {
            if (in_array($failedItem, self::CRITICAL_PARAMETERS, true)) {
                return QualityReading::STATUS_CRITICAL;
            }
        }

        return QualityReading::STATUS_WARNING;
    }
}
