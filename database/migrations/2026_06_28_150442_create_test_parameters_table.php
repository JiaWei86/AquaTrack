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
        Schema::create('test_parameters', function (Blueprint $table) {

            $table->id();

            $table->foreignId('lab_test_id')
                  ->constrained('lab_tests')
                  ->cascadeOnDelete();

            $table->string('parameter_name');

            $table->decimal('parameter_value', 8, 2);

            $table->string('unit');

            $table->enum('status', [
                'Normal',
                'Abnormal'
            ]);

            $table->decimal('safe_min', 8, 2)->nullable();

            $table->decimal('safe_max', 8, 2)->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_parameters');
    }
};