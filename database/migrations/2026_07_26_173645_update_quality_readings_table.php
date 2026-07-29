<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quality_readings', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Remove Old Column
            |--------------------------------------------------------------------------
            */

            $table->dropColumn('bacteria_count');

            /*
            |--------------------------------------------------------------------------
            | River / Lake Parameters
            |--------------------------------------------------------------------------
            */

            $table->decimal('bod', 5, 2)->nullable()->after('dissolved_oxygen');
            $table->decimal('cod', 6, 2)->nullable()->after('bod');
            $table->decimal('suspended_solids', 6, 2)->nullable()->after('cod');
            $table->decimal('ammoniacal_nitrogen', 5, 2)->nullable()->after('suspended_solids');

            /*
            |--------------------------------------------------------------------------
            | Well Parameters
            |--------------------------------------------------------------------------
            */

            $table->decimal('tds', 8, 2)->nullable()->after('conductivity');
            $table->decimal('hardness', 8, 2)->nullable()->after('tds');
            $table->decimal('chloride', 8, 2)->nullable()->after('hardness');
            $table->decimal('sulphate', 8, 2)->nullable()->after('chloride');
            $table->decimal('nitrate', 6, 2)->nullable()->after('sulphate');
            $table->decimal('iron', 5, 2)->nullable()->after('nitrate');
            $table->decimal('manganese', 5, 2)->nullable()->after('iron');

            /*
            |--------------------------------------------------------------------------
            | Community Tap Parameters
            |--------------------------------------------------------------------------
            */

            $table->decimal('colour', 5, 2)->nullable()->after('manganese');
            $table->decimal('residual_chlorine', 4, 2)->nullable()->after('colour');
            $table->decimal('aluminium', 5, 2)->nullable()->after('residual_chlorine');

            $table->boolean('total_coliform')->nullable()->after('aluminium');
            $table->boolean('e_coli')->nullable()->after('total_coliform');

            /*
            |--------------------------------------------------------------------------
            | Assessment Method
            |--------------------------------------------------------------------------
            */

            $table->string('assessment_method')->nullable()->after('wqi');
        });

        /*
        |--------------------------------------------------------------------------
        | Modify Existing Columns
        |--------------------------------------------------------------------------
        */

        Schema::table('quality_readings', function (Blueprint $table) {

            $table->string('classification')->nullable()->change();

            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quality_readings', function (Blueprint $table) {

            // Restore old column
            $table->integer('bacteria_count')->default(0);

            // Remove newly added columns
            $table->dropColumn([
                'bod',
                'cod',
                'suspended_solids',
                'ammoniacal_nitrogen',

                'tds',
                'hardness',
                'chloride',
                'sulphate',
                'nitrate',
                'iron',
                'manganese',

                'colour',
                'residual_chlorine',
                'aluminium',

                'total_coliform',
                'e_coli',

                'assessment_method',
            ]);
        });

        Schema::table('quality_readings', function (Blueprint $table) {

            $table->enum('classification', [
                'Clean',
                'Slightly Polluted',
                'Polluted'
            ])->change();

            $table->enum('status', [
                'Normal',
                'Abnormal'
            ])->change();
        });
    }
};