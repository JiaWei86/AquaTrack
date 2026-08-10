<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
        'state',
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

    public static function malaysianStates(): array
    {
        return [
            'Johor',
            'Kedah',
            'Kelantan',
            'Malacca',
            'Negeri Sembilan',
            'Pahang',
            'Penang',
            'Perak',
            'Perlis',
            'Sabah',
            'Sarawak',
            'Selangor',
            'Terengganu',
            'Kuala Lumpur',
            'Labuan',
            'Putrajaya',
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
