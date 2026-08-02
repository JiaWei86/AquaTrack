<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE quality_readings MODIFY temperature DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY dissolved_oxygen DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY bod DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY cod DECIMAL(6, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY suspended_solids DECIMAL(6, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY ammoniacal_nitrogen DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY conductivity DECIMAL(7, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY tds DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY hardness DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY chloride DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY sulphate DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY nitrate DECIMAL(6, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY iron DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY manganese DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY turbidity DECIMAL(6, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY colour DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY residual_chlorine DECIMAL(4, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY aluminium DECIMAL(5, 2) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY total_coliform TINYINT(1) NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY e_coli TINYINT(1) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE quality_readings MODIFY temperature DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY dissolved_oxygen DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY bod DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY cod DECIMAL(6, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY suspended_solids DECIMAL(6, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY ammoniacal_nitrogen DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY conductivity DECIMAL(7, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY tds DECIMAL(8, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY hardness DECIMAL(8, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY chloride DECIMAL(8, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY sulphate DECIMAL(8, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY nitrate DECIMAL(6, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY iron DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY manganese DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY turbidity DECIMAL(6, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY colour DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY residual_chlorine DECIMAL(4, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY aluminium DECIMAL(5, 2) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY total_coliform TINYINT(1) NOT NULL');
        DB::statement('ALTER TABLE quality_readings MODIFY e_coli TINYINT(1) NOT NULL');
    }
};
