<?php

namespace App\Services\Factory;

use App\Models\User;

abstract class UserFactory
{
    abstract protected function role(): string;

    public function create(array $attributes): User
    {
        $payload = array_merge($attributes, ['role' => $this->role()]);

        return User::create($payload);
    }
}
