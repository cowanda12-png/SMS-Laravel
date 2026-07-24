<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('admission_number')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('status', ['active', 'inactive', 'graduated', 'suspended', 'expelled'])->default('active');
                $table->string('registration_number')->nullable()->unique();
                $table->timestamps();
                
                // Indexes for better performance
                $table->index('admission_number');
                $table->index('email');
                $table->index('class_id');
                $table->index('course_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};