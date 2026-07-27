<?php
// database/migrations/2026_07_27_create_fee_structures_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('fee_type');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->string('term')->nullable();
            $table->string('academic_year')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_compulsory')->default(true);
            $table->date('due_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            
            // No foreign key constraints to avoid errors
            // We'll add them later after the tables exist
            
            $table->unique(['fee_type', 'class_id', 'grade_id', 'term', 'academic_year'], 'unique_fee_structure');
            $table->index('fee_type');
            $table->index('status');
            $table->index('term');
            $table->index('academic_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};