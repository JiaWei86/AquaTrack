<?php

namespace App\States;

use App\Models\Complaint;
use App\Models\User;

class InvestigatingState implements ComplaintState
{
    public function getName(): string
    {
        return 'Investigating';
    }

    public function allowedTransitions(): array
    {
        // Once under investigation it can only be closed
        return ['Resolved', 'Rejected'];
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }

    public function canBeDeletedBy(User $user, Complaint $complaint): bool
    {
        // The resident can no longer withdraw a complaint that is
        // already being investigated - only an administrator may delete it.
        return $user->isAdministrator();
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function getStatusMessage(): string
    {
        return 'An inspector is currently investigating your complaint.';
    }

    public function getBadgeClass(): string
    {
        return 'badge-aqua';
    }
}