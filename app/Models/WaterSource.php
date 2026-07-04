<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_name',
        'source_type',
        'location',
        'latitude',
        'longitude',
        'notes',
    ];

    /**
     * A water source can have many complaints.
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * A water source can have many quality readings.
     */
    public function qualityReadings()
    {
        return $this->hasMany(QualityReading::class);
    }

    /**
     * A water source can have many alerts.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}