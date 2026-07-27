<?php
// database/migrations/2026_07_27_create_exams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['quiz', 'assignment', 'midterm', 'final', 'practical', 'project']);
            $table->date('exam_date');
            $table->date('submission_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->decimal('passing_score', 5, 2)->default(50);
            $table->decimal('weight', 5, 2)->default(100);
            $table->enum('status', ['draft', 'published', 'completed', 'graded'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->text('instructions')->nullable();
            $table->timestamps();
            
            $table->index(['course_id', 'class_id']);
            $table->index('exam_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};