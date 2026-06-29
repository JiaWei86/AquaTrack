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
        Schema::create('alerts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('water_source_id')
                  ->constrained('water_sources')
                  ->cascadeOnDelete();

            $table->foreignId('quality_reading_id')
                  ->constrained('quality_readings')
                  ->cascadeOnDelete();

            $table->string('message');

            $table->enum('severity', [
                'Low',
                'Medium',
                'High'
            ]);

            $table->enum('status', [
                'Active',
                'Resolved'
            ])->default('Active');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};