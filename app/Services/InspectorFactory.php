<?php

namespace App\Services;

class InspectorFactory extends UserFactory
{
    protected function role(): string
    {
        return 'Inspector';
    }
}
