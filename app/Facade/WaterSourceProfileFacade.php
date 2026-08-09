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

        $totalComplaints = $waterSource->complaints()->count();
        $totalAlerts     = $waterSource->alerts()->count();

        return [
            'stats' => [
                [
                    'label'      => 'Complaints',
                    'value'      => $totalComplaints,
                    'link'       => route('water-sources.summary', [$waterSource, 'complaints']),
                    'alert'      => $hasOpenComplaints,
                    'percentage' => $this->getOpenComplaintPercentage($waterSource, $totalComplaints),
                ],
                [
                    'label'      => 'Quality Readings',
                    'value'      => $waterSource->qualityReadings()->count(),
                    'link'       => route('water-sources.summary', [$waterSource, 'quality-readings']),
                    'alert'      => $hasQualityIssue,
                    'percentage' => null,
                ],
                [
                    'label'      => 'Alerts',
                    'value'      => $totalAlerts,
                    'link'       => route('water-sources.summary', [$waterSource, 'alerts']),
                    'alert'      => $hasActiveAlert,
                    'percentage' => $this->getActiveAlertPercentage($waterSource, $totalAlerts),
                ],
            ],
            'needs_attention' => $hasOpenComplaints || $hasQualityIssue || $hasActiveAlert,
            'quality_trend'   => $this->getQualityTrend($waterSource),
        ];
    }

    /**
     * Compare the two most recent QualityReading records' wqi values to
     * determine whether water quality is trending up, down, or holding
     * steady. Returns null when there's nothing meaningful to compare
     * (fewer than 2 readings, or either reading has no wqi recorded).
     */
    public function getQualityTrend(WaterSource $waterSource): ?string
    {
        $readings = $waterSource->qualityReadings()
            ->latest()
            ->limit(2)
            ->get();

        if ($readings->count() < 2) {
            return null;
        }

        $latest = $readings->first();
        $previous = $readings->last();

        if ($latest->wqi === null || $previous->wqi === null) {
            return null;
        }

        if ((float) $latest->wqi > (float) $previous->wqi) {
            return 'improving';
        }

        if ((float) $latest->wqi < (float) $previous->wqi) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Percentage (0-100) of complaints that are still open (Pending or
     * Investigating), given the water source's already-known total
     * complaint count. Null when $totalComplaints is 0, to avoid a
     * meaningless 0/0 division.
     */
    public function getOpenComplaintPercentage(WaterSource $waterSource, int $totalComplaints): ?int
    {
        if ($totalComplaints === 0) {
            return null;
        }

        $open = $waterSource->complaints()
            ->whereIn('status', ['Pending', 'Investigating'])
            ->count();

        return (int) round(($open / $totalComplaints) * 100);
    }

    /**
     * Percentage (0-100) of alerts that are still Active, given the
     * water source's already-known total alert count. Null when
     * $totalAlerts is 0, to avoid a meaningless 0/0 division.
     */
    public function getActiveAlertPercentage(WaterSource $waterSource, int $totalAlerts): ?int
    {
        if ($totalAlerts === 0) {
            return null;
        }

        $active = $waterSource->alerts()
            ->where('status', 'Active')
            ->count();

        return (int) round(($active / $totalAlerts) * 100);
    }

    /**
     * Interpret the Complaint subsystem: does this water source have any
     * complaint that hasn't reached a resolved (or rejected) state yet?
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