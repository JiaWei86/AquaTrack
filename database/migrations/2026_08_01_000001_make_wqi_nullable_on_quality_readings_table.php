<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE quality_readings
            MODIFY wqi DECIMAL(5, 2) NULL
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE quality_readings
            MODIFY wqi DECIMAL(5, 2) NOT NULL
        ');
    }
};
