<?php

namespace App\Services\Factory;

class AdministratorFactory extends UserFactory
{
    protected function role(): string
    {
        return 'Administrator';
    }
}
