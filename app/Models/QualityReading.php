<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspector_id',
        'water_source_id',
        'ph',
        'temperature',
        'turbidity',
        'bacteria_count',
        'dissolved_oxygen',
        'conductivity',
        'remarks',
    ];

    /**
     * The inspector who recorded this reading.
     */
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * The water source being monitored.
     */
    public function waterSource()
    {
        return $this->belongsTo(WaterSource::class);
    }

    /**
     * Get all alerts generated from this quality reading.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}