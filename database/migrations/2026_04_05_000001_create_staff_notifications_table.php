<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('target_role');           // 'Admin', 'Receptionist', or 'All'
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->string('type');                  // 'report_approved', 'report_on_hold'
            $table->string('title');
            $table->string('message');
            $table->timestamps();
        });

        Schema::create('staff_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['staff_notification_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_notification_reads');
        Schema::dropIfExists('staff_notifications');
    }
};
