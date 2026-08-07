<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\QualityReading;

class AlertService
{
    public function createOrUpdateAlert(QualityReading $qualityReading): void
    {
        if ($qualityReading->status === 'Safe') {
            return;
        }

        $qualityReading->loadMissing('waterSource');

        $waterSourceName = $qualityReading->waterSource?->source_name ?? 'the selected water source';

        if ($qualityReading->status === 'Warning') {
            $severity = 'Medium';
            $message = "Water quality warning detected at {$waterSourceName}.";
        } elseif ($qualityReading->status === 'Critical') {
            $severity = 'High';
            $message = "Critical water quality detected at {$waterSourceName}. Immediate investigation is required.";
        } else {
            return;
        }

        $alert = $this->activeAlertForWaterSource($qualityReading);

        if ($alert) {
            $alert->quality_reading_id = $qualityReading->id;
            $alert->message = $message;
            $alert->severity = $severity;
            $alert->save();

            return;
        }

        Alert::create([
            'water_source_id' => $qualityReading->water_source_id,
            'quality_reading_id' => $qualityReading->id,
            'message' => $message,
            'severity' => $severity,
            'status' => 'Active',
        ]);
    }

    public function resolveAlert(QualityReading $qualityReading): void
    {
        $alert = $this->activeAlertForWaterSource($qualityReading);

        if (! $alert) {
            return;
        }

        $alert->status = 'Resolved';
        $alert->save();
    }

    private function activeAlertForWaterSource(QualityReading $qualityReading): ?Alert
    {
        return Alert::where('water_source_id', $qualityReading->water_source_id)
            ->where('status', 'Active')
            ->first();
    }
}
