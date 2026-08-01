<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\States\ComplaintState;
use App\States\PendingState;
use App\States\InvestigatingState;
use App\States\ResolvedState;
use App\States\RejectedState;

class Complaint extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * State Pattern (Context):
     * Resolves the current status string into its state object.
     */
    public function state(): ComplaintState
    {
        return match ($this->status) {
            'Pending'       => new PendingState(),
            'Investigating' => new InvestigatingState(),
            'Resolved'      => new ResolvedState(),
            'Rejected'      => new RejectedState(),
        };
    }

    /**
     * Delegates the transition decision to the current state object.
     * The state itself decides whether the transition is legal.
     */
    public function transitionTo(string $newStatus): bool
    {
        if (!$this->state()->canTransitionTo($newStatus)) {
            return false;
        }

        $this->update(['status' => $newStatus]);

        return true;
    }

    /**
     * Delegates the delete permission to the current state.
     */
    public function canBeDeletedBy(User $user): bool
    {
        return $this->state()->canBeDeletedBy($user, $this);
    }
}