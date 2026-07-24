<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign key to fees table
        if (Schema::hasTable('fees') && Schema::hasTable('students')) {
            try {
                Schema::table('fees', function (Blueprint $table) {
                    $table->foreign('student_id')
                          ->references('id')
                          ->on('students')
                          ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }
        
        // Add foreign key to mpesa_transactions table
        if (Schema::hasTable('mpesa_transactions') && Schema::hasTable('students')) {
            try {
                Schema::table('mpesa_transactions', function (Blueprint $table) {
                    $table->foreign('student_id')
                          ->references('id')
                          ->on('students')
                          ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('fees')) {
            try {
                Schema::table('fees', function (Blueprint $table) {
                    $table->dropForeign(['student_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }
        
        if (Schema::hasTable('mpesa_transactions')) {
            try {
                Schema::table('mpesa_transactions', function (Blueprint $table) {
                    $table->dropForeign(['student_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }
    }
};