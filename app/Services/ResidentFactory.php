<?php

namespace App\Services;

class ResidentFactory extends UserFactory
{
    protected function role(): string
    {
        return 'Resident';
    }
}
