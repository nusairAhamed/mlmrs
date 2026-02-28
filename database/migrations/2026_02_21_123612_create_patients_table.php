<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('patient_code', 50)->unique();
            $table->string('full_name', 150);
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index(['full_name']);
            $table->index(['phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};