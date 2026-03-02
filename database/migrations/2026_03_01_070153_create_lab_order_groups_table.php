<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_order_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_group_id')->constrained()->restrictOnDelete();

            $table->decimal('group_price_snapshot', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['lab_order_id', 'test_group_id']);
            $table->index(['lab_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_order_groups');
    }
};