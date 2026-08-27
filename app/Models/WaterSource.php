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
        'created_by',
    ];

    /**
     * Strong typing for numeric coordinate fields, so latitude/longitude
     * are always handled as decimals (not raw strings) wherever the model
     * is read — this is part of enforcing strongly-typed data access.
     */
    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

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

    /**
     * The administrator who created this water source record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}