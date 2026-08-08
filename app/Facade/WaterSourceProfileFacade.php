<?php

namespace App\Facade;

use App\Models\WaterSource;

class WaterSourceProfileFacade
{
    public function getSummary(WaterSource $waterSource): array
    {
        $hasOpenComplaints = $this->hasOpenComplaints($waterSource);
        $hasQualityIssue   = $this->hasQualityIssue($waterSource);
        $hasActiveAlert    = $this->hasActiveAlert($waterSource);

        return [
            'stats' => [
                [
                    'label' => 'Complaints',
                    'value' => $waterSource->complaints()->count(),
                    'link'  => route('water-sources.summary', [$waterSource, 'complaints']),
                    'alert' => $hasOpenComplaints,
                ],
                [
                    'label' => 'Quality Readings',
                    'value' => $waterSource->qualityReadings()->count(),
                    'link'  => route('water-sources.summary', [$waterSource, 'quality-readings']),
                    'alert' => $hasQualityIssue,
                ],
                [
                    'label' => 'Alerts',
                    'value' => $waterSource->alerts()->count(),
                    'link'  => route('water-sources.summary', [$waterSource, 'alerts']),
                    'alert' => $hasActiveAlert,
                ],
            ],
            'needs_attention' => $hasOpenComplaints || $hasQualityIssue || $hasActiveAlert,
        ];
    }

    /**
     * Interpret the Complaint subsystem: does this water source have any
     * complaint that hasn't reached a resolved state yet?
     */
    private function hasOpenComplaints(WaterSource $waterSource): bool
    {
        return $waterSource->complaints()
            ->where('status', '!=', 'Resolved')
            ->where('status', '!=', 'Rejected')
            ->exists();
    }

    /**
     * Interpret the QualityReading subsystem: is the most recent reading
     * for this water source classified as Critical?
     */
    private function hasQualityIssue(WaterSource $waterSource): bool
    {
        $latestReading = $waterSource->qualityReadings()->latest()->first();

        return $latestReading?->status === 'Critical';
    }

    /**
     * Interpret the Alert subsystem: is there any alert still Active for
     * this water source?
     */
    private function hasActiveAlert(WaterSource $waterSource): bool
    {
        return $waterSource->alerts()
            ->where('status', 'Active')
            ->exists();
    }
}