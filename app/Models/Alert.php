<?php

namespace App\Models;

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
}