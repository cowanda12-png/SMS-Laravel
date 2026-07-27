<?php
// database/migrations/2026_07_27_create_grade_scales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('grade');
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('remark')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Insert default grade scales
        DB::table('grade_scales')->insert([
            ['grade' => 'A', 'min_score' => 80, 'max_score' => 100, 'remark' => 'Excellent', 'color' => '#28a745', 'is_default' => true, 'order' => 1],
            ['grade' => 'B', 'min_score' => 70, 'max_score' => 79.99, 'remark' => 'Very Good', 'color' => '#17a2b8', 'is_default' => true, 'order' => 2],
            ['grade' => 'C', 'min_score' => 60, 'max_score' => 69.99, 'remark' => 'Good', 'color' => '#ffc107', 'is_default' => true, 'order' => 3],
            ['grade' => 'D', 'min_score' => 50, 'max_score' => 59.99, 'remark' => 'Satisfactory', 'color' => '#fd7e14', 'is_default' => true, 'order' => 4],
            ['grade' => 'E', 'min_score' => 40, 'max_score' => 49.99, 'remark' => 'Below Average', 'color' => '#dc3545', 'is_default' => true, 'order' => 5],
            ['grade' => 'F', 'min_score' => 0, 'max_score' => 39.99, 'remark' => 'Fail', 'color' => '#dc3545', 'is_default' => true, 'order' => 6],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};