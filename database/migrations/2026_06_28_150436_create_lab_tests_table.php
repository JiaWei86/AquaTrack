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
        Schema::create('lab_tests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('inspector_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('water_source_id')
                  ->constrained('water_sources')
                  ->cascadeOnDelete();

            $table->foreignId('quality_reading_id')
                  ->constrained('quality_readings')
                  ->cascadeOnDelete();

            $table->decimal('wqi', 5, 2);

            $table->enum('classification', [
                'Clean',
                'Slightly Polluted',
                'Polluted'
            ]);

            $table->text('remarks')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};