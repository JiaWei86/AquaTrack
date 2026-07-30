<?php

namespace App\Services;

use InvalidArgumentException;

class UserFactoryProducer
{
    public static function factory(string $role): UserFactory
    {
        return match ($role) {
            'Inspector' => new InspectorFactory(),
            'Resident' => new ResidentFactory(),
            'Administrator' => new AdministratorFactory(),
            default => throw new InvalidArgumentException("Unsupported user role: {$role}"),
        };
    }
}
