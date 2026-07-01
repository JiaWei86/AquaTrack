<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get all complaints submitted by this resident.
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'resident_id');
    }
    
    /**
     * Get all quality readings recorded by this inspector.
     */
    public function qualityReadings()
    {
        return $this->hasMany(QualityReading::class, 'inspector_id');
    }
    /**
     * Get all lab tests performed by this inspector.
     */
    public function labTests()
    {
        return $this->hasMany(LabTest::class, 'inspector_id');
    }

    /**
     * Check if the user is a resident.
     */
    public function isResident(): bool
    {
        return $this->role === 'Resident';
    }

    /**
     * Check if the user is an inspector.
     */
    public function isInspector(): bool
    {
        return $this->role === 'Inspector';
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'Administrator';
    }

}
