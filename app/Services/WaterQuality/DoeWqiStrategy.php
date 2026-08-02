<?php

namespace App\Services\WaterQuality;

use App\Models\QualityReading;
use App\Services\WaterQuality\Contracts\WaterQualityStrategy;
use InvalidArgumentException;

class DoeWqiStrategy implements WaterQualityStrategy
{
    private const ASSESSMENT_METHOD = 'DOE-WQI';
    private const CLASSIFICATION_CLEAN = 'Clean';
    private const CLASSIFICATION_SLIGHTLY_POLLUTED = 'Slightly Polluted';
    private const CLASSIFICATION_POLLUTED = 'Polluted';

    private const REQUIRED_PARAMETERS = [
        'ph',
        'dissolved_oxygen',
        'temperature',
        'bod',
        'cod',
        'suspended_solids',
        'ammoniacal_nitrogen',
    ];

    private const DO_SATURATION_BY_TEMPERATURE = [
        0 => 14.62,
        5 => 12.77,
        10 => 11.29,
        15 => 10.08,
        20 => 9.09,
        25 => 8.26,
        30 => 7.56,
        35 => 6.95,
        40 => 6.41,
    ];

    private const SUB_INDEX_MIN = 0.0;
    private const SUB_INDEX_MAX = 100.0;
    private const WQI_CLEAN_MIN = 81.0;
    private const WQI_SLIGHTLY_POLLUTED_MIN = 60.0;
    private const WQI_DECIMAL_PLACES = 2;
    private const POWER_TWO = 2;
    private const POWER_THREE = 3;
    private const DO_SATURATION_INTERVAL = 5.0;
    private const DO_SATURATION_FIRST_UPPER_OFFSET = 1;

    private const WQI_WEIGHT_SIDO = 0.22;
    private const WQI_WEIGHT_SIBOD = 0.19;
    private const WQI_WEIGHT_SICOD = 0.16;
    private const WQI_WEIGHT_SIAN = 0.15;
    private const WQI_WEIGHT_SISS = 0.16;
    private const WQI_WEIGHT_SIPH = 0.12;

    private const DO_PERCENT_LOW_MAX = 8.0;
    private const DO_PERCENT_HIGH_MIN = 92.0;
    private const SIDO_CUBIC_INTERCEPT = -0.395;
    private const SIDO_QUADRATIC_COEFFICIENT = 0.030;
    private const SIDO_CUBIC_COEFFICIENT = -0.00020;

    private const BOD_LINEAR_MAX = 5.0;
    private const SIBOD_LINEAR_INTERCEPT = 100.4;
    private const SIBOD_LINEAR_SLOPE = 4.23;
    private const SIBOD_EXP_MULTIPLIER = 108.0;
    private const SIBOD_EXP_COEFFICIENT = -0.055;
    private const SIBOD_HIGH_SLOPE = 0.1;

    private const COD_LINEAR_MAX = 20.0;
    private const SICOD_LINEAR_INTERCEPT = 99.1;
    private const SICOD_LINEAR_SLOPE = 1.33;
    private const SICOD_EXP_MULTIPLIER = 103.0;
    private const SICOD_EXP_COEFFICIENT = -0.0157;
    private const SICOD_HIGH_SLOPE = 0.04;

    private const AN_LINEAR_MAX = 0.3;
    private const AN_ZERO_MIN = 4.0;
    private const AN_OFFSET = 2.0;
    private const SIAN_LINEAR_INTERCEPT = 100.5;
    private const SIAN_LINEAR_SLOPE = 105.0;
    private const SIAN_EXP_MULTIPLIER = 94.0;
    private const SIAN_EXP_COEFFICIENT = -0.573;
    private const SIAN_ABS_COEFFICIENT = 5.0;

    private const SS_LOW_MAX = 100.0;
    private const SS_ZERO_MIN = 1000.0;
    private const SISS_LOW_EXP_MULTIPLIER = 97.5;
    private const SISS_LOW_EXP_COEFFICIENT = -0.00676;
    private const SISS_LOW_SLOPE = 0.05;
    private const SISS_HIGH_EXP_MULTIPLIER = 71.0;
    private const SISS_HIGH_EXP_COEFFICIENT = -0.0061;
    private const SISS_HIGH_SLOPE = 0.015;

    private const PH_LOW_MAX = 5.5;
    private const PH_NEUTRAL_MIN = 7.0;
    private const PH_HIGH_MIN = 8.75;
    private const SIPH_LOW_INTERCEPT = 17.2;
    private const SIPH_LOW_LINEAR = -17.2;
    private const SIPH_LOW_QUADRATIC = 5.02;
    private const SIPH_MID_LOW_INTERCEPT = -242.0;
    private const SIPH_MID_LOW_LINEAR = 95.5;
    private const SIPH_MID_LOW_QUADRATIC = -6.67;
    private const SIPH_MID_HIGH_INTERCEPT = -181.0;
    private const SIPH_MID_HIGH_LINEAR = 82.4;
    private const SIPH_MID_HIGH_QUADRATIC = -6.05;
    private const SIPH_HIGH_INTERCEPT = 536.0;
    private const SIPH_HIGH_LINEAR = -77.0;
    private const SIPH_HIGH_QUADRATIC = 2.76;

    /**
     * Calculate DOE-WQI assessment results from required river water quality data.
     *
     * @throws InvalidArgumentException
     */
    public function calculate(array $data): array
    {
        foreach (self::REQUIRED_PARAMETERS as $parameter) {
            if (! array_key_exists($parameter, $data)) {
                throw new InvalidArgumentException("Missing DOE-WQI parameter: {$parameter}");
            }
        }

        $doPercent = $this->calculateDoPercent(
            (float) $data['dissolved_oxygen'],
            (float) $data['temperature']
        );

        $wqi = $this->calculateFinalWqi(
            $this->calculateSIDO($doPercent),
            $this->calculateSIBOD((float) $data['bod']),
            $this->calculateSICOD((float) $data['cod']),
            $this->calculateSISS((float) $data['suspended_solids']),
            $this->calculateSIAN((float) $data['ammoniacal_nitrogen']),
            $this->calculateSIpH((float) $data['ph'])
        );

        return [
            'assessment_method' => self::ASSESSMENT_METHOD,
            'wqi' => $wqi,
            'classification' => $this->determineClassification($wqi),
            'status' => $this->determineStatus($wqi),
        ];
    }

    /**
     * Calculate oxygen saturation at a temperature using linear interpolation.
     */
    private function calculateDoSaturation(float $temperature): float
    {
        $temperatures = array_keys(self::DO_SATURATION_BY_TEMPERATURE);
        $firstTemperature = $temperatures[array_key_first($temperatures)];
        $lastTemperature = $temperatures[array_key_last($temperatures)];

        if ($temperature <= $firstTemperature) {
            return self::DO_SATURATION_BY_TEMPERATURE[$firstTemperature];
        }

        if ($temperature >= $lastTemperature) {
            return self::DO_SATURATION_BY_TEMPERATURE[$lastTemperature];
        }

        foreach (array_slice($temperatures, self::DO_SATURATION_FIRST_UPPER_OFFSET) as $upperTemperature) {
            $lowerTemperature = $upperTemperature - self::DO_SATURATION_INTERVAL;

            if ($temperature <= $upperTemperature) {
                $lowerValue = self::DO_SATURATION_BY_TEMPERATURE[$lowerTemperature];
                $upperValue = self::DO_SATURATION_BY_TEMPERATURE[$upperTemperature];

                return $lowerValue
                    + (($temperature - $lowerTemperature) / self::DO_SATURATION_INTERVAL) * ($upperValue - $lowerValue);
            }
        }

        return self::DO_SATURATION_BY_TEMPERATURE[$lastTemperature];
    }

    /**
     * Calculate dissolved oxygen as percentage saturation.
     */
    private function calculateDoPercent(float $dissolvedOxygen, float $temperature): float
    {
        return ($dissolvedOxygen / $this->calculateDoSaturation($temperature)) * self::SUB_INDEX_MAX;
    }

    /**
     * Calculate the DOE sub-index for dissolved oxygen.
     */
    private function calculateSIDO(float $doPercent): float
    {
        if ($doPercent <= self::DO_PERCENT_LOW_MAX) {
            return self::SUB_INDEX_MIN;
        }

        if ($doPercent >= self::DO_PERCENT_HIGH_MIN) {
            return self::SUB_INDEX_MAX;
        }

        $score = self::SIDO_CUBIC_INTERCEPT
            + self::SIDO_QUADRATIC_COEFFICIENT * ($doPercent ** self::POWER_TWO)
            + self::SIDO_CUBIC_COEFFICIENT * ($doPercent ** self::POWER_THREE);

        return max(self::SUB_INDEX_MIN, min(self::SUB_INDEX_MAX, $score));
    }

    /**
     * Calculate the DOE sub-index for biochemical oxygen demand.
     */
    private function calculateSIBOD(float $bod): float
    {
        if ($bod <= self::BOD_LINEAR_MAX) {
            $score = self::SIBOD_LINEAR_INTERCEPT - self::SIBOD_LINEAR_SLOPE * $bod;
        } else {
            $score = self::SIBOD_EXP_MULTIPLIER * exp(self::SIBOD_EXP_COEFFICIENT * $bod)
                - self::SIBOD_HIGH_SLOPE * $bod;
        }

        return max(self::SUB_INDEX_MIN, min(self::SUB_INDEX_MAX, $score));
    }

    /**
     * Calculate the DOE sub-index for chemical oxygen demand.
     */
    private function calculateSICOD(float $cod): float
    {
        if ($cod <= self::COD_LINEAR_MAX) {
            $score = self::SICOD_LINEAR_INTERCEPT - self::SICOD_LINEAR_SLOPE * $cod;
        } else {
            $score = self::SICOD_EXP_MULTIPLIER * exp(self::SICOD_EXP_COEFFICIENT * $cod)
                - self::SICOD_HIGH_SLOPE * $cod;
        }

        return max(self::SUB_INDEX_MIN, min(self::SUB_INDEX_MAX, $score));
    }

    /**
     * Calculate the DOE sub-index for suspended solids.
     */
    private function calculateSISS(float $suspendedSolids): float
    {
        if ($suspendedSolids <= self::SS_LOW_MAX) {
            $score = self::SISS_LOW_EXP_MULTIPLIER * exp(self::SISS_LOW_EXP_COEFFICIENT * $suspendedSolids)
                + self::SISS_LOW_SLOPE * $suspendedSolids;
        } elseif ($suspendedSolids < self::SS_ZERO_MIN) {
            $score = self::SISS_HIGH_EXP_MULTIPLIER * exp(self::SISS_HIGH_EXP_COEFFICIENT * $suspendedSolids)
                - self::SISS_HIGH_SLOPE * $suspendedSolids;
        } else {
            $score = self::SUB_INDEX_MIN;
        }

        return max(self::SUB_INDEX_MIN, min(self::SUB_INDEX_MAX, $score));
    }

    /**
     * Calculate the DOE sub-index for ammoniacal nitrogen.
     */
    private function calculateSIAN(float $ammoniacalNitrogen): float
    {
        if ($ammoniacalNitrogen <= self::AN_LINEAR_MAX) {
            $score = self::SIAN_LINEAR_INTERCEPT - self::SIAN_LINEAR_SLOPE * $ammoniacalNitrogen;
        } elseif ($ammoniacalNitrogen < self::AN_ZERO_MIN) {
            $score = self::SIAN_EXP_MULTIPLIER * exp(self::SIAN_EXP_COEFFICIENT * $ammoniacalNitrogen)
                - self::SIAN_ABS_COEFFICIENT * abs($ammoniacalNitrogen - self::AN_OFFSET);
        } else {
            $score = self::SUB_INDEX_MIN;
        }

        return max(self::SUB_INDEX_MIN, min(self::SUB_INDEX_MAX, $score));
    }

    /**
     * Calculate the DOE sub-index for pH.
     */
    private function calculateSIpH(float $ph): float
    {
        if ($ph < self::PH_LOW_MAX) {
            $score = self::SIPH_LOW_INTERCEPT + self::SIPH_LOW_LINEAR * $ph + self::SIPH_LOW_QUADRATIC * ($ph ** self::POWER_TWO);
        } elseif ($ph < self::PH_NEUTRAL_MIN) {
            $score = self::SIPH_MID_LOW_INTERCEPT + self::SIPH_MID_LOW_LINEAR * $ph + self::SIPH_MID_LOW_QUADRATIC * ($ph ** self::POWER_TWO);
        } elseif ($ph < self::PH_HIGH_MIN) {
            $score = self::SIPH_MID_HIGH_INTERCEPT + self::SIPH_MID_HIGH_LINEAR * $ph + self::SIPH_MID_HIGH_QUADRATIC * ($ph ** self::POWER_TWO);
        } else {
            $score = self::SIPH_HIGH_INTERCEPT + self::SIPH_HIGH_LINEAR * $ph + self::SIPH_HIGH_QUADRATIC * ($ph ** self::POWER_TWO);
        }

        return max(self::SUB_INDEX_MIN, min(self::SUB_INDEX_MAX, $score));
    }

    /**
     * Calculate and round the final DOE-WQI value.
     */
    private function calculateFinalWqi(
        float $sido,
        float $sibod,
        float $sicod,
        float $siss,
        float $sian,
        float $siph
    ): float {
        $wqi = self::WQI_WEIGHT_SIDO * $sido
            + self::WQI_WEIGHT_SIBOD * $sibod
            + self::WQI_WEIGHT_SICOD * $sicod
            + self::WQI_WEIGHT_SISS * $siss
            + self::WQI_WEIGHT_SIAN * $sian
            + self::WQI_WEIGHT_SIPH * $siph;

        return round($wqi, self::WQI_DECIMAL_PLACES);
    }

    /**
     * Determine the water quality classification from DOE-WQI.
     */
    private function determineClassification(float $wqi): string
    {
        if ($wqi >= self::WQI_CLEAN_MIN) {
            return self::CLASSIFICATION_CLEAN;
        }

        if ($wqi >= self::WQI_SLIGHTLY_POLLUTED_MIN) {
            return self::CLASSIFICATION_SLIGHTLY_POLLUTED;
        }

        return self::CLASSIFICATION_POLLUTED;
    }

    /**
     * Determine the database status value from DOE-WQI.
     */
    private function determineStatus(float $wqi): string
    {
        if ($wqi >= 80) {
            return QualityReading::STATUS_SAFE;
        }

        if ($wqi >= 60) {
            return QualityReading::STATUS_WARNING;
        }

        return QualityReading::STATUS_CRITICAL;
    }
}
