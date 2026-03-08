<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('lab_order_id')->constrained('lab_orders')->cascadeOnDelete();
            $table->enum('channel', ['email', 'sms']);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('message');
            $table->text('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};