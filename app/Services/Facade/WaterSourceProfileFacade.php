<?php

namespace App\Services\Facade;

use App\Models\Alert;
use App\Models\Complaint;
use App\Models\QualityReading;
use App\Models\WaterSource;
use Illuminate\Database\Eloquent\Collection;

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
                            'label'      => 'Complaints',
                            'value'      => $waterSource->complaints()->count(),
                            'type'       => 'complaints',
                            'alert'      => $hasOpenComplaints,
                            'percentage' => $this->getOpenComplaintPercentage($waterSource),
                        ],
                        [
                            'label'      => 'Quality Readings',
                            'value'      => $waterSource->qualityReadings()->count(),
                            'type'       => 'quality-readings',
                            'alert'      => $hasQualityIssue,
                            'percentage' => null,
                        ],
                        [
                            'label'      => 'Alerts',
                            'value'      => $waterSource->alerts()->count(),
                            'type'       => 'alerts',
                            'alert'      => $hasActiveAlert,
                            'percentage' => $this->getActiveAlertPercentage($waterSource),
                        ],
                    ],
            'needs_attention' => $hasOpenComplaints || $hasQualityIssue || $hasActiveAlert,
            'quality_trend'   => $this->getQualityTrend($waterSource),
        ];
    }

    /**
     * Fetch the raw records for one related type (complaints, quality
     * readings, or alerts) for a water source, ordered newest first.
     * Used by the summary page, which needs the actual records rather
     * than an aggregated count — routed through the Facade so the
     * controller never talks to the subsystems directly.
     */
    public function getItems(WaterSource $waterSource, string $type)
    {
        return match ($type) {
            'complaints'       => $waterSource->complaints()->latest()->get(),
            'quality-readings' => $waterSource->qualityReadings()->latest()->get(),
            'alerts'           => $waterSource->alerts()->latest()->get(),
        };
    }

    /**
     * Group already-fetched $items by their status column, seeded with
     * every status this type can have (even ones with zero occurrences)
     * so a caller can render a complete, consistent legend. Returns null
     * for types that don't have a meaningful status breakdown (quality
     * readings use a WQI trend instead). Delegates entirely to the
     * relevant subsystem, which owns the list of known statuses.
     */
    public function getStatusBreakdown(Collection $items, string $type): ?array
    {
        return match ($type) {
            'complaints' => Complaint::statusBreakdown($items),
            'alerts'     => Alert::statusBreakdown($items),
            default      => null,
        };
    }

    /**
     * Compare the two most recent QualityReading records' wqi values to
     * determine whether water quality is trending up, down, or holding
     * steady. Delegates entirely to the QualityReading subsystem.
     */
    private function getQualityTrend(WaterSource $waterSource): ?string
    {
        return QualityReading::trendForWaterSource($waterSource);
    }

    /**
     * Percentage of complaints still open. Delegates entirely to the
     * Complaint subsystem.
     */
    private function getOpenComplaintPercentage(WaterSource $waterSource): ?int
    {
        return Complaint::openPercentageForWaterSource($waterSource);
    }

    /**
     * Percentage of alerts still Active. Delegates entirely to the
     * Alert subsystem.
     */
    private function getActiveAlertPercentage(WaterSource $waterSource): ?int
    {
        return Alert::activePercentageForWaterSource($waterSource);
    }

    /**
     * Interpret the Complaint subsystem: does this water source have any
     * complaint that's still open? Delegates to Complaint::isOpen().
     */
    private function hasOpenComplaints(WaterSource $waterSource): bool
    {
        return $waterSource->complaints->contains(fn ($complaint) => $complaint->isOpen());
    }

    /**
     * Interpret the QualityReading subsystem: is the most recent reading
     * for this water source classified as Critical?
     */
    private function hasQualityIssue(WaterSource $waterSource): bool
    {
        $latestReading = $waterSource->qualityReadings()->latest()->first();

        return $latestReading?->isCritical() ?? false;
    }

    /**
     * Interpret the Alert subsystem: is there any alert still Active for
     * this water source? Delegates to Alert::scopeActive().
     */
    private function hasActiveAlert(WaterSource $waterSource): bool
    {
        return $waterSource->alerts()->active()->exists();
    }
}