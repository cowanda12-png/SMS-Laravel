<?php
// database/migrations/2026_07_27_205458_create_performance_tracking_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('term')->nullable();
            $table->string('academic_year')->nullable();
            $table->decimal('average_score', 5, 2)->default(0);
            $table->decimal('cumulative_average', 5, 2)->default(0);
            $table->string('overall_grade')->nullable();
            $table->integer('rank')->nullable();
            $table->integer('total_students')->nullable();
            $table->json('subject_breakdown')->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->timestamps();
            
            // FIXED: Shorter unique constraint name
            $table->unique(['student_id', 'course_id', 'term', 'academic_year'], 'perf_tracking_unique');
            $table->index(['student_id', 'course_id']);
            $table->index(['term', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_tracking');
    }
};