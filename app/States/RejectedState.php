<?php

namespace App\States;

use App\Models\Complaint;
use App\Models\User;

class RejectedState implements ComplaintState
{
    public function getName(): string
    {
        return 'Rejected';
    }

    public function allowedTransitions(): array
    {
        // Final state
        return [];
    }

    public function canTransitionTo(string $status): bool
    {
        return false;
    }

    public function canBeDeletedBy(User $user, Complaint $complaint): bool
    {
        return $user->isAdministrator();
    }

    public function isFinal(): bool
    {
        return true;
    }

    public function getStatusMessage(): string
    {
        return 'This complaint was rejected after review.';
    }

    public function getBadgeClass(): string
    {
        return 'bg-danger';
    }
}