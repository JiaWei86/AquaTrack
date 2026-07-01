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
        Schema::create('water_sources', function (Blueprint $table) {

            $table->id();

    $table->string('source_name');

    $table->enum('source_type', [
        'River',
        'Lake',
        'Reservoir',
        'Well',
        'Community Tap'
    ]);

    $table->string('location');

    $table->decimal('latitude', 10, 7);

    $table->decimal('longitude', 10, 7);

    $table->text('notes')->nullable();

    $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_sources');
    }
};
