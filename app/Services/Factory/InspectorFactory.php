<?php

namespace App\Services\Factory;

class InspectorFactory extends UserFactory
{
    protected function role(): string
    {
        return 'Inspector';
    }
}
