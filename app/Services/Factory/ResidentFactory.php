<?php

namespace App\Services\Factory;

class ResidentFactory extends UserFactory
{
    protected function role(): string
    {
        return 'Resident';
    }
}
