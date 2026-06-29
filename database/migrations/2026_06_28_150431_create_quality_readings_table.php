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
        Schema::create('quality_readings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('inspector_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('water_source_id')
                  ->constrained('water_sources')
                  ->cascadeOnDelete();

            $table->decimal('ph', 4, 2);

            $table->decimal('temperature', 5, 2);

            $table->decimal('turbidity', 6, 2);

            $table->integer('bacteria_count');

            $table->decimal('dissolved_oxygen', 5, 2);

            $table->decimal('conductivity', 7, 2);

            $table->text('remarks')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_readings');
    }
};