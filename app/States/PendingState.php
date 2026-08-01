<?php

namespace App\States;

use App\Models\Complaint;
use App\Models\User;

class PendingState implements ComplaintState
{
    public function getName(): string
    {
        return 'Pending';
    }

    public function allowedTransitions(): array
    {
        // A new complaint may be picked up for investigation or rejected
        return ['Investigating', 'Rejected'];
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }

    public function canBeDeletedBy(User $user, Complaint $complaint): bool
    {
        // Residents may withdraw their own complaint while it is still Pending;
        // administrators may delete any complaint.
        return $user->isAdministrator()
            || ($user->isResident() && $complaint->resident_id === $user->id);
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function getStatusMessage(): string
    {
        return 'Your complaint has been received and is waiting to be reviewed.';
    }

    public function getBadgeClass(): string
    {
        return 'bg-warning text-dark';
    }
}