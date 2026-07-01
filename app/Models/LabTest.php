<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspector_id',
        'water_source_id',
        'quality_reading_id',
        'wqi',
        'classification',
        'remarks',
    ];

    /**
     * The inspector who performed this lab test.
     */
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * The related water source.
     */
    public function waterSource()
    {
        return $this->belongsTo(WaterSource::class);
    }

    /**
     * The quality reading associated with this lab test.
     */
    public function qualityReading()
    {
        return $this->belongsTo(QualityReading::class);
    }

    /**
     * Lab test contains many test parameters.
     */
    public function testParameters()
    {
        return $this->hasMany(TestParameter::class);
    }
}