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
        Schema::create('test_reference_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();

            $table->enum('gender', ['any', 'male', 'female'])->default('any');

            $table->unsignedInteger('age_min')->nullable();  // years
            $table->unsignedInteger('age_max')->nullable();  // years

            $table->decimal('ref_min', 10, 2)->nullable();
            $table->decimal('ref_max', 10, 2)->nullable();

            $table->index(['test_id', 'gender']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_reference_ranges');
    }
};
