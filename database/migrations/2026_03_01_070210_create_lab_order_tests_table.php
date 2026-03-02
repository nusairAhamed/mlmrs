<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_order_tests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_order_group_id')->nullable()->constrained('lab_order_groups')->nullOnDelete();

            $table->foreignId('test_id')->constrained()->restrictOnDelete();

            // snapshots
            $table->string('test_name', 150);
            $table->string('unit', 30)->nullable();

            // reference range snapshot
            $table->foreignId('test_reference_range_id')->nullable()
                ->constrained('test_reference_ranges')
                ->nullOnDelete();

            $table->decimal('ref_min', 12, 4)->nullable();
            $table->decimal('ref_max', 12, 4)->nullable();

            // result
            $table->string('result_value', 100)->nullable();
            $table->boolean('is_abnormal')->default(false);

            $table->string('status', 20)->default('pending'); // pending, entered, verified(optional)

            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('entered_at')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->unique(['lab_order_id', 'test_id']);
            $table->index(['lab_order_id', 'status']);
            $table->index(['is_abnormal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_order_tests');
    }
};