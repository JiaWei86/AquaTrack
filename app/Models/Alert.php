<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'water_source_id',
        'quality_reading_id',
        'message',
        'severity',
        'status',
    ];

    /**
     * The water source related to this alert.
     */
    public function waterSource()
    {
        return $this->belongsTo(WaterSource::class);
    }

    /**
     * The quality reading that triggered this alert.
     */
    public function qualityReading()
    {
        return $this->belongsTo(QualityReading::class);
    }

    /**
     * Scope a query to only Active alerts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Percentage (0-100) of the given water source's alerts that are
     * still Active. Null when there are no alerts at all, to avoid a
     * meaningless 0/0 division.
     */
    public static function activePercentageForWaterSource(WaterSource $waterSource): ?int
    {
        $total = $waterSource->alerts()->count();

        if ($total === 0) {
            return null;
        }

        $active = $waterSource->alerts()->active()->count();

        return (int) round(($active / $total) * 100);
    }

    /**
     * Group the given alerts by status, seeded with every known status
     * (even ones with zero occurrences) so callers get a complete,
     * consistent breakdown for rendering a legend.
     */
    public static function statusBreakdown(Collection $items): array
    {
        $knownStatuses = ['Active', 'Resolved'];

        $counts = $items->countBy('status');

        return collect($knownStatuses)
            ->mapWithKeys(fn ($status) => [$status => $counts->get($status, 0)])
            ->all();
    }
}