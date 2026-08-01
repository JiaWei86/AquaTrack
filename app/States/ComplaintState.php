<?php

namespace App\States;

use App\Models\Complaint;
use App\Models\User;

interface ComplaintState
{
    /** The status name stored in the database. */
    public function getName(): string;

    /** Statuses this state is allowed to transition into. */
    public function allowedTransitions(): array;

    /** Whether a transition to the given status is legal. */
    public function canTransitionTo(string $status): bool;

    /** Whether the complaint may be deleted by the given user. */
    public function canBeDeletedBy(User $user, Complaint $complaint): bool;

    /** Whether this is a final state (no further transitions). */
    public function isFinal(): bool;

    /** Message shown to the resident describing the current stage. */
    public function getStatusMessage(): string;

    /** Bootstrap badge class used to render this state. */
    public function getBadgeClass(): string;
}