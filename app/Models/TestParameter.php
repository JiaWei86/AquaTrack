<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_test_id',
        'parameter_name',
        'parameter_value',
        'unit',
        'status',
        'safe_min',
        'safe_max',
    ];

    /**
     * Test Parameter belongs to a Lab Test.
     */
    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }
}