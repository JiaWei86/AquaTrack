<?php

namespace App\Services;

class AdministratorFactory extends UserFactory
{
    protected function role(): string
    {
        return 'Administrator';
    }
}
