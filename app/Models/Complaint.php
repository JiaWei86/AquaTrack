<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'water_source_id',
        'title',
        'description',
        'photo',
        'status',
    ];

    /**
     * Complaint belongs to a Resident (User)
     */
    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    /**
     * Complaint belongs to a Water Source
     */
    public function waterSource()
    {
        return $this->belongsTo(WaterSource::class);
    }
}