<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityReading extends Model
{
    use HasFactory;

    public const STATUS_SAFE = 'Safe';
    public const STATUS_WARNING = 'Warning';
    public const STATUS_CRITICAL = 'Critical';

    protected $fillable = [
        'inspector_id',
        'water_source_id',
        'sample_date',
        'ph',
        'temperature',
        'turbidity',
        'dissolved_oxygen',
        'bod',
        'cod',
        'suspended_solids',
        'ammoniacal_nitrogen',
        'conductivity',
        'tds',
        'hardness',
        'chloride',
        'sulphate',
        'nitrate',
        'iron',
        'manganese',
        'colour',
        'residual_chlorine',
        'aluminium',
        'total_coliform',
        'e_coli',
        'wqi',
        'assessment_method',
        'classification',
        'status',
        'remarks',
    ];

    protected $casts = [
        'sample_date' => 'date',
        'ph' => 'decimal:2',
        'temperature' => 'decimal:2',
        'turbidity' => 'decimal:2',
        'dissolved_oxygen' => 'decimal:2',
        'bod' => 'decimal:2',
        'cod' => 'decimal:2',
        'suspended_solids' => 'decimal:2',
        'ammoniacal_nitrogen' => 'decimal:2',
        'conductivity' => 'decimal:2',
        'tds' => 'decimal:2',
        'hardness' => 'decimal:2',
        'chloride' => 'decimal:2',
        'sulphate' => 'decimal:2',
        'nitrate' => 'decimal:2',
        'iron' => 'decimal:2',
        'manganese' => 'decimal:2',
        'colour' => 'decimal:2',
        'residual_chlorine' => 'decimal:2',
        'aluminium' => 'decimal:2',
        'total_coliform' => 'boolean',
        'e_coli' => 'boolean',
        'wqi' => 'decimal:2',
    ];

    /**
     * Get the inspector who recorded this reading.
     */
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Get the water source associated with this reading.
     */
    public function waterSource()
    {
        return $this->belongsTo(WaterSource::class);
    }

    /**
     * Get all alerts generated from this reading.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
 * Whether this reading's status is Critical.
 */
    public function isCritical(): bool
    {
        return $this->status === self::STATUS_CRITICAL;
    }

    /**
     * Compare the two most recent readings for a given water source and
     * return whether WQI is trending up, down, or holding steady. Returns
     * null when there's nothing meaningful to compare (fewer than 2
     * readings, or either reading has no wqi recorded).
     */
    public static function trendForWaterSource(WaterSource $waterSource): ?string
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
}