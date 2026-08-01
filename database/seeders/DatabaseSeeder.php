<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WaterSource;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial test data.
     */
    public function run(): void
    {
        // Test accounts — one for each role
        User::create([
            'name'     => 'Test Resident',
            'email'    => 'resident@test.com',
            'password' => 'password',   // auto-hashed by the 'hashed' cast in User model
            'role'     => 'Resident',
            'phone'    => '012-3456789',
        ]);

        User::create([
            'name'     => 'Test Inspector',
            'email'    => 'inspector@test.com',
            'password' => 'password',
            'role'     => 'Inspector',
            'phone'    => '012-2345678',
        ]);

        User::create([
            'name'     => 'Test Admin',
            'email'    => 'admin@test.com',
            'password' => 'password',
            'role'     => 'Administrator',
            'phone'    => '012-1234567',
        ]);

        \App\Models\User::create([
            'name'     => 'Test Resident 2',
            'email'    => 'resident2@test.com',
            'password' => 'password',
            'role'     => 'Resident',
            'phone'    => '012-9999999',
        ]);

        // Water sources — test data (around Rawang, Selangor)
        WaterSource::create([
            'source_name' => 'Sungai Rawang Intake Point',
            'source_type' => 'River',
            'location'    => 'Rawang, Selangor',
            'latitude'    => 3.3213500,
            'longitude'   => 101.5767800,
            'notes'       => 'Main river intake serving Rawang town area.',
        ]);

        WaterSource::create([
            'source_name' => 'Taman Rawang Perdana Community Tap',
            'source_type' => 'Community Tap',
            'location'    => 'Taman Rawang Perdana, Rawang',
            'latitude'    => 3.3178200,
            'longitude'   => 101.5723400,
            'notes'       => null,
        ]);

        WaterSource::create([
            'source_name' => 'Kundang Lake Reservoir',
            'source_type' => 'Reservoir',
            'location'    => 'Kundang, Selangor',
            'latitude'    => 3.2812600,
            'longitude'   => 101.5334900,
            'notes'       => 'Backup supply during dry season.',
        ]);
    }
}