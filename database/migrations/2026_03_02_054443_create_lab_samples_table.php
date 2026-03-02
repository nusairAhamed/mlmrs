<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_samples', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lab_order_id')
                ->constrained('lab_orders')
                ->cascadeOnDelete();

            $table->string('sample_code', 50)->unique();

            // Keep as string for flexibility (blood, urine, serum, plasma, etc.)
            $table->string('sample_type', 50)->index();

            // Status lifecycle of physical sample
            $table->enum('status', [
                'collected',
                'received',
                'in_process',
                'rejected',
                'completed',
            ])->default('collected')->index();

            $table->dateTime('collected_at')->nullable();
            $table->dateTime('received_at')->nullable();

            $table->string('rejected_reason', 255)->nullable();

            $table->timestamps();

            // Helpful composite index for lookups
            $table->index(['lab_order_id', 'sample_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_samples');
    }
};