<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Whether this complaint is still open (i.e. hasn't reached a final
     * state). Delegates entirely to the State pattern already in place —
     * Resolved and Rejected are both final, everything else is open.
     */
    public function isOpen(): bool
    {
        return ! $this->state()->isFinal();
    }

    /**
     * Percentage (0-100) of the given water source's complaints that are
     * still open. Null when there are no complaints at all, to avoid a
     * meaningless 0/0 division.
     */
    public static function openPercentageForWaterSource(WaterSource $waterSource): ?int
    {
        $total = $waterSource->complaints->count();

        if ($total === 0) {
            return null;
        }

        $open = $waterSource->complaints->filter(fn ($complaint) => $complaint->isOpen())->count();

        return (int) round(($open / $total) * 100);
    }

    /**
     * Group the given complaints by status, seeded with every known
     * status (even ones with zero occurrences) so callers get a
     * complete, consistent breakdown for rendering a legend.
     */
    public static function statusBreakdown(Collection $items): array
    {
        $knownStatuses = ['Pending', 'Investigating', 'Resolved', 'Rejected'];

        $counts = $items->countBy('status');

        return collect($knownStatuses)
            ->mapWithKeys(fn ($status) => [$status => $counts->get($status, 0)])
            ->all();
    }
}