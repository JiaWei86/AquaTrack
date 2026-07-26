<?php

namespace App\States;

use App\Models\Complaint;
use App\Models\User;

class ResolvedState implements ComplaintState
{
    public function getName(): string
    {
        return 'Resolved';
    }

    public function allowedTransitions(): array
    {
        // Final state - a resolved complaint cannot be reopened
        return [];
    }

    public function canTransitionTo(string $status): bool
    {
        return false;
    }

    public function canBeDeletedBy(User $user, Complaint $complaint): bool
    {
        // Resolved complaints are kept as records; only admins may remove them
        return $user->isAdministrator();
    }

    public function isFinal(): bool
    {
        return true;
    }

    public function getStatusMessage(): string
    {
        return 'This complaint has been resolved. Thank you for your report.';
    }

    public function getBadgeClass(): string
    {
        return 'bg-success';
    }
}